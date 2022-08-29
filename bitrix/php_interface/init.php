<?
/*
You can place here your functions and event handlers

AddEventHandler("module", "EventName", "FunctionName");
function FunctionName(params)
{
	//code
}
*/

\Bitrix\Main\Loader::registerAutoLoadClasses(
    null,
    [
        '\App\Organizations' => '/app/classes/Organizations.php',
    ]
);

?>