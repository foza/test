<?php
function find()
{

    $mysqli = new mysqli("localhost", "root", "", "billing");
    if (mysqli_connect_errno()) {
        printf("Connection failed: %s\n", mysqli_connect_error());
        exit();
    }
    $mysqli->set_charset("utf8");

    $code = file_get_contents('http://www.bills.ru');
    $doc = new DomDocument('1.0', 'utf-8');
    @$doc->loadHTML($code);
    $xp = new DomXPath($doc);
    $date = array();
    $title = array();
    $url = array();
    $count = 0;
    foreach ($xp->query('//*[@id="bizon_api_news_list"]/tr/td[@class="news_date"]') as $node) {
        array_push($date, $node->nodeValue);
        $count++;
        if ($count === 5) {
            $count = 0;
            break;
        }
    };
    foreach ($xp->query('//*[@id="bizon_api_news_list"]/tr/td/a') as $node) {
        array_push($title, $node->nodeValue);
        $count++;
        if ($count === 5) {
            $count = 0;
            break;
        }
    };
    foreach ($xp->query('//*[@id="bizon_api_news_list"]/tr/td/a') as $node) {
        array_push($url, $node->getAttribute('href'));
        $count++;
        if ($count === 5) {
            break;
        }
    };
    $months = array(
        "янв" => '01',
        "фев" => '02',
        "мар" => '03',
        "апр" => '04',
        "май" => '05',
        "июн" => '06',
        "июл" => '07',
        "авг" => '08',
        "сен" => '09',
        "окт" => '10',
        "ноя" => '11',
        "дек" => '12'
    );
    for ($i = 0; $i < count($date); $i++) {
        preg_match("/^\s*([0-9]*)\s*([а-я]*)\s*([0-9]*)/u", $date[$i], $result);
        $date[$i] = "$result[3]-" . $months[$result[2]] . "-" . $result[1] . " 00:00:00";
    }


    $values = "";
    for ($i = 0; $i < count($date); $i++) {
        if ($i === count($date) - 1) {
            $values .= "('" . $date[$i] . "', '" . $title[$i] . "', '" . $url[$i] . "')";
        } else {
            $values .= "('" . $date[$i] . "', '" . $title[$i] . "', '" . $url[$i] . "'), ";
        }
    }

    $truncateQuery = "TRUNCATE TABLE `bills_ru_events`";
    $insertQuery = "INSERT INTO `bills_ru_events` (`date`, `title`, `url`) VALUES $values";
    try {
        if (!$mysqli->query($truncateQuery)) {
            throw new Exception('Не удалось очистить таблицу bills_ru_events - ' . $mysqli->error);
        } elseif (!$mysqli->query($insertQuery)) {
            throw new Exception('Не удалось записать данные в таблицу bills_ru_events - ' . $mysqli->error);
        } else {
            echo 'Таблица bills_ru_events заполнена данными с сайта www.bills.ru';
        }
    } catch (Exception $e) {
        echo '<br>Исключение: ', $e->getMessage(), "\n";
    }
}

