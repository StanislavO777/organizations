<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("");
$APPLICATION->SetPageProperty("title", "Демонстрационная версия продукта «1С-Битрикс: Управление сайтом»");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");

use App\Organizations;

$organizations = new Organizations();
$elements = $organizations->getOrganizationsElements();

if ($elements == false) { // получить организации из Яндекс карт и сохранить ?>
    <script>
        jQuery.getJSON('https://search-maps.yandex.ru/v1/?text=ПАО%20Газпромнефть%20нефтегазовая%20компания,Нефтегазовая%20компания&results=6&type=biz&lang=ru_RU&apikey=e6cb9dbb-13e5-4b59-a393-73bfb6972b9e', function (json) {

            let obj = {};

            for (let i = 0; i < json.features.length; i++) {

                let phone = json.features[i].properties.CompanyMetaData.Phones;

                if (phone === undefined) {
                    phone = '';
                }

                console.log(json.features[i]);
                obj[i] = {
                    coordinates: json.features[i].geometry.coordinates,
                    name: json.features[i].properties.name,
                    address: json.features[i].properties.CompanyMetaData.address,
                    phone: phone[0]
                };
            }

            $.ajax({
                cache: false,
                url: 'ajax.php',
                method: 'POST',
                data: obj,
                dataType: 'json',
                success: function (data) {

                    console.log(data);
                    if (data.finish === true) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                }
            });
        });
    </script>
<? } else { // вывести организации из инфоблока ?>
        <div class="container">
            <div class="row d-flex justify-content-center">
                <h1>Карта офисов компании</h1>
            </div>
            <div class="row d-flex justify-content-center">
                <p>Ограничение выборки через API: 6 элементов</p>
            </div>
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-center">
                    <div id="map" style="width: 400px; height: 300px"></div>
                </div>
            </div>
        </div>


    <script>
        ymaps.ready(init);

        function init() {
            var myMap = new ymaps.Map("map", {
                center: [55.76, 37.64],
                zoom: 2
            }, {
                searchControlProvider: 'yandex#search'
            });

            myMap.geoObjects
            <? foreach ($elements as $elVal): ?>
                .add(new ymaps.Placemark([<?= $elVal['PROPERTY_OFFICE_COORDINATES_VALUE'] ?>], {
                    balloonContent: 'Название: <strong><?= $elVal['PROPERTY_OFFICE_NAME_VALUE'] ?></strong></br>Телефон: <strong><?= $elVal['PROPERTY_OFFICE_PHONE_VALUE'] ?></strong></br>Телефон: <strong><?= $elVal['PROPERTY_OFFICE_CITY_VALUE'] ?></strong>'
                }, {
                    preset: 'islands#icon',
                    iconColor: '#0095b6'
                }))
            <? endforeach; ?>
        }
    </script>
<? }