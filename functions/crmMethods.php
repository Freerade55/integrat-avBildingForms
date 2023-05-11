<?php


//  Выводит по id сущность, можно передать любую. Сделку, компанию и тд
function getEntity(string $entity_type, int $id): array
{
    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts/$id?with=leads";
            break;
        case CRM_ENTITY_LEAD:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads/$id?with=contacts";
            break;
        case CRM_ENTITY_COMPANY:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies/$id?with=contacts";
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }


}





//  Ищет сущность по строке, можно передать любую. Сделку, компанию и тд.
function searchEntity(string $entity_type, string $search): array
{


    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $query = [
                "with" => "leads",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts?" . http_build_query($query);
            break;
        case CRM_ENTITY_LEAD:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads?" . http_build_query($query);
            break;
        case CRM_ENTITY_COMPANY:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies?" . http_build_query($query);
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }

}





// добавление контакта
function addContact(string $ContactName, string $phone) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts";



    $queryData = array(

        [

            "name" => $ContactName,

            "responsible_user_id" => CRM_RESPONSIBLE_ID,

            "custom_fields_values" => [
                [
                    "field_id" => 194295,
                    "values" => [
                        [
                            "value" => $phone
                        ]
                    ]
                ]

            ]



        ]



    );




    return json_decode(connect($link, METHOD_POST, $queryData), true);




}










// добавление сделки
function addLead(int $contact_Id, int $responseUserId = CRM_RESPONSIBLE_ID) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads";

    $queryData = array(

        [

            "name" => "Заявка с сайта",

            "responsible_user_id" => $responseUserId,
            "pipeline_id" => CRM_PIPELINE_ID,

            "_embedded" => [
                "contacts" => [
                    [
                        "id" => $contact_Id
                    ]
                ]
            ]



        ]




    );






    return json_decode(connect($link, METHOD_POST, $queryData), true);




}



// добавление примечания
function addNote(string $entityType, int $entityId, string $description) {



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/$entityType/$entityId/notes";
    $queryData = array(

        [

            "note_type" => "common",
            "params" => [
                "text" => $description
            ]


        ]




    );

    return json_decode(connect($link, METHOD_POST, $queryData), true);




}



// добавление задачи по дублю контакта
function addTask(int $leadId, int $responsible_user_id = CRM_RESPONSIBLE_ID)
{



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/tasks";


    $endWorkDayStr = date("Y-m-d 18:00:00");
    $endWorkDayUnix= strtotime($endWorkDayStr);

    $now = time();

    $dayNumber = date("N");

    if($dayNumber == 5) {
        $res = strtotime(date("Y-m-d 18:00:00", strtotime($endWorkDayStr.'+ 3 days')));
    } else if($dayNumber == 6) {
        $res = strtotime(date("Y-m-d 18:00:00", strtotime($endWorkDayStr.'+ 2 days')));

    } else if($now >= $endWorkDayUnix) {
        $res = strtotime(date("Y-m-d 18:00:00", strtotime($endWorkDayStr.'+ 1 days')));

    } else {
        $before = $endWorkDayUnix - $now;
        $res = $now + $before;
    }



    $queryData = array(
        [
            "text" => "Заявка с сайта",
            "entity_id" => $leadId,
            "complete_till" => $res,
            "entity_type" => "leads",
            "responsible_user_id" => $responsible_user_id

        ]
    );

    json_decode(connect($link, METHOD_POST, $queryData), true);



}




////    устанавливает тег из списка
function addTag(string $value, int $leadId)

{



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/$value";

    $queryData = array(

        [
            "id" => $leadId,
            "_embedded" => [
                "tags" => [
                    [
                        "id" => CRM_TAG_ID
                    ]
                ]

            ]
        ]
    );

    connect($link, 'PATCH', $queryData);


}






