<?php
// Необходимые заголовки 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Content-type: application/json");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Подключаем файлы
require_once "src/functions.php";
require_once "src/constants.php";
require_once "src/AmoCrm.php";
require_once "src/Vk.php";
// Получение данных хука
$data = json_decode(file_get_contents('php://input'), true);
try {
    // Создаем экземпляры классов
    $amoV4Client = new AmoCrmV4Client(SUB_DOMAIN, CLIENT_ID, CLIENT_SECRET, CODE, REDIRECT_URL);
    $vk = new vk(VK_TOKEN);
    // id vk пользователя
    $vk_id = $data['object']['message']['from_id'];
    // Получаем имя для поиска
    $name = $vk->getName($vk_id);
    $contact = $amoV4Client->POSTRequestApi(
        'contacts',
        ['query' => $name, 'whith' => 'leads']
    )['_embedded']['contacts'];

    if (!empty($contact)) {
        // Добавляем id в поле
        $lead = $contact[0]['_embedded']['leads'][0]['id'];
        $params =
            [
                'pipeline_id' => PIPELINE_ID,
                'status_id' => STATUS_ID
            ];
        $params[0]['custom_fields_values'] = [
            "field_id" => FILD_VK_ID,
            "values" => [
                [
                    "value" => $data['comment'],
                ]
            ]
        ];
        $amoV4Client->POSTRequestApi('leads/' . $lead, $params);
    } else
        throw new Exception('Контакт не найден', 404);
} catch (Exception $ex) {
    http_response_code($ex->getCode());
    echo json_encode([
        'message' => $ex->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    Write('main_errors', 'Ошибка: ' . $ex->getMessage() . PHP_EOL . 'Код ошибки:' . $ex->getCode());
}
