<?php


namespace App;

use CModule;

class Organizations
{
    /**
     * @param $iblockCode
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function getIblockId($iblockCode)
    {
        $iblockId = \Bitrix\Iblock\IblockTable::getList(['filter' => ['CODE' => $iblockCode]])->Fetch()['ID'];
        return $iblockId;
    }

    /**
     * Получаем список элементов ИБ Организации
     *
     * @return array|bool
     */
    public static function getOrganizationsElements()
    {

        CModule::IncludeModule("iblock");

        $organizationsIb = \CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                'IBLOCK_ID' => self::getIblockId('organizations'),
                'ACTIVE' => 'Y'
            ],
            false,
            false,
            ['ID', 'PROPERTY_OFFICE_NAME', 'PROPERTY_OFFICE_PHONE', 'PROPERTY_OFFICE_EMAIL', 'PROPERTY_OFFICE_COORDINATES', 'PROPERTY_OFFICE_CITY']

        );

        if ($organizationsIb->AffectedRowsCount() > 0) {
            $elements = [];

            while ($element = $organizationsIb->Fetch()) {
                $elements[] = $element;
            }

            return $elements;
        } else {
            return false;
        }
    }

    /**
     * Входящий массив с организациями
     * Сохраняем данные в ИБ Организации
     *
     * @param $arr
     * @return bool
     */
    public static function saveOrganizationsElement($arr)
    {
        CModule::IncludeModule("iblock");

        foreach ($arr as $org) {
            $address = explode(', ', $org['address']);

            $el = new \CIBlockElement;

            $arProps = [
                "OFFICE_NAME" => $org['name'],
                "OFFICE_PHONE" => $org['phone']['formatted'] ?? '',
                "OFFICE_EMAIL" => '',
                "OFFICE_COORDINATES" => $org['coordinates'][1] . ',' . $org['coordinates'][0],
                "OFFICE_CITY" => $address[1],
            ];

            $arFields = [
                "IBLOCK_ID" => self::getIblockId('organizations'),
                "NAME" => $org['name'],
                "PROPERTY_VALUES" => $arProps,
                "ACTIVE" => "Y"
            ];

            $el->Add($arFields);
        }

        return true;
    }
}