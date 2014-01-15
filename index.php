<?php
//error_reporting();
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$iblock_id = isset($_GET['iblock_id'])? intval($_GET['iblock_id']) : 0;
if (0 < $iblock_id) {
    CModule::IncludeModule('iblock');
    if (CIBlock::GetByID($iblock_id)->Fetch()) {
        $fname = isset($_GET['fname'])? trim($_GET['fname']) : 'xml_export.xml';
        $xml_writer = new XMLWriter;
        $xml_writer->openUri(__DIR__ . '/' . $fname);
        $xml_writer->startDocument('1.0', 'utf-8');
        $xml_writer->startElement('iblock_elements');

        $db_items = CIBlockElement::GetList(
            array(),
            array('IBLOCK_ID' => $iblock_id),
            false,
            false,
            array('ID', 'NAME', 'PREVIEW_TEXT')
        );
        while ($row = $db_items->Fetch()) {
            $xml_writer->startElement('iblock_element');

            $xml_writer->startElement('element_id');
            $xml_writer->text($row['ID']);
            $xml_writer->endElement();

            $xml_writer->startElement('element_name');
            $xml_writer->text($row['NAME']);
            $xml_writer->endElement();

            if (!empty($row['PREVIEW_TEXT'])) {
                $xml_writer->startElement('element_text');
                $xml_writer->text($row['PREVIEW_TEXT']);
                $xml_writer->endElement();
            }

            $xml_writer->endElement();
        }
        $xml_writer->endDocument();
        $xml_writer->flush();

        echo 'Файл ' . $fname . ' успешно создан.';
    } else {
        echo 'Инфоблок с указанным ИД не существует.';
    }
} else {
    echo 'Указанный ИД инфоблока меньше или равен нулю.';
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
