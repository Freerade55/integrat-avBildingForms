<?php
header('Content-Type: text/html; charset=utf-8');

const ROOT = __DIR__;

require ROOT . "/functions/require.php";

// форма с заказом техники

// form_name = Заказать технику
// модель техники - Type
// Телефон - Телефон
// имя клиента - Имя
// комментарий - Комментарий

// форма со звонком
// form_name = Обратный звонок
// Телефон - Телефон





$data = $_REQUEST;

logs($data);

//$data = [
//    "form_name" => "Заказать технику",
//    "Type" => "КАМАЗ 43118 (самосвал-вездеход)",
//    "Телефон" => "+7-(332)-323-2341",
//    "имя клиента" => "name",
//    "комментарий" => "878787675",
//
//
//];


if(!empty($data)) {



    if ($data["form_name"] == "Заказать технику") {



        $model = $data["Type"];
        $name = $data["Имя"];
        $phone = $data["Телефон"];
        $comment = $data["Комментарий"];


        $phone = preg_replace("/[^\d]/siu", "", $phone);
        $phone = preg_replace("/^[8]/", "7", $phone, 1);




        $searchContact = searchEntity(CRM_ENTITY_CONTACT, $phone);


        $description = "имя - $name \n \n 
        телефон - $phone \n \n 
        модель - $model \n \n
        комментарий - $comment \n \n ";


        if (!empty($searchContact)) {


            $contactId = $searchContact["_embedded"]["contacts"][0]["id"];
//        ответственный с контакта, в случае если уже есть контакт
            $responsible_user_id = $searchContact["_embedded"]["contacts"][0]["responsible_user_id"];

            $addedLeadId = null;


            //      если есть сделки вообще

            if (!empty($searchContact["_embedded"]["contacts"][0]["_embedded"]["leads"])) {


                $leads = $searchContact["_embedded"]["contacts"][0]["_embedded"]["leads"];

                $activeLeadStatus = null;

                foreach ($leads as $lead) {

// поиск седелок по id
                    $getLeadInfo = getEntity(CRM_ENTITY_LEAD, $lead["id"]);


//            проверка на статус сделки
                    if ($getLeadInfo["status_id"] != 142 && $getLeadInfo["status_id"] != 143) {

                        $activeLeadStatus = true;
                        $addedLeadId = $lead["id"];

                    }


                }


//       если нет активных сделок

                if (!isset($activeLeadStatus)) {


                    $addLeadRes = addLead($contactId, $responsible_user_id);
                    $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

                    addTag("leads", $addedLeadId);


                }


            } else {

                $addLeadRes = addLead($contactId, $responsible_user_id);
                $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

                addTag("leads", $addedLeadId);


            }


            addNote("contacts", $contactId, $description);

            addTask($addedLeadId, $responsible_user_id);


        } else {

            $addContact = addContact($name, $phone);


            $addLeadRes = addLead($addContact["_embedded"]["contacts"][0]["id"]);
            $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

            addTag("leads", $addedLeadId);

            addNote("contacts", $addContact["_embedded"]["contacts"][0]["id"], $description);

            addTask($addedLeadId);



        }


    } else {


        $phone = $data["Телефон"];


        $phone = preg_replace("/[^\d]/siu", "", $phone);
        $phone = preg_replace("/^[8]/", "7", $phone, 1);
        $name = "Контакт $phone";





        $searchContact = searchEntity(CRM_ENTITY_CONTACT, $phone);


        $description = "
        телефон - $phone \n \n ";



        if (!empty($searchContact)) {


            $contactId = $searchContact["_embedded"]["contacts"][0]["id"];
//        ответственный с контакта, в случае если уже есть контакт
            $responsible_user_id = $searchContact["_embedded"]["contacts"][0]["responsible_user_id"];

            $addedLeadId = null;


            //      если есть сделки вообще

            if (!empty($searchContact["_embedded"]["contacts"][0]["_embedded"]["leads"])) {


                $leads = $searchContact["_embedded"]["contacts"][0]["_embedded"]["leads"];

                $activeLeadStatus = null;

                foreach ($leads as $lead) {

// поиск седелок по id
                    $getLeadInfo = getEntity(CRM_ENTITY_LEAD, $lead["id"]);


//            проверка на статус сделки
                    if ($getLeadInfo["status_id"] != 142 && $getLeadInfo["status_id"] != 143) {

                        $activeLeadStatus = true;
                        $addedLeadId = $lead["id"];

                    }


                }


//       если нет активных сделок

                if (!isset($activeLeadStatus)) {


                    $addLeadRes = addLead($contactId, $responsible_user_id);
                    $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

                    addTag("leads", $addedLeadId);


                }


            } else {

                $addLeadRes = addLead($contactId, $responsible_user_id);
                $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

                addTag("leads", $addedLeadId);


            }


            addNote("contacts", $contactId, $description);

            addTask($addedLeadId, $responsible_user_id);


        } else {

            $addContact = addContact($name, $phone);


            $addLeadRes = addLead($addContact["_embedded"]["contacts"][0]["id"]);
            $addedLeadId = $addLeadRes["_embedded"]["leads"][0]["id"];

            addTag("leads", $addedLeadId);

            addNote("contacts", $addContact["_embedded"]["contacts"][0]["id"], $description);

            addTask($addedLeadId);



        }






    }





}






























