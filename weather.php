<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if (!empty($argv[1])) {
    parse_str($argv[1], $_GET);
}

if ($_GET['test'] == 'apples' && $_GET['x'] == 'arbitrary' && isset($_GET['p'])) {
    $ch = curl_init("https://api.weather.gov/gridpoints/ILN/43,81/forecast");
    $agents = ['Chrome','Firefox','Opera','Edge'];
    $randomAgent = rand(0,3);
    $randomAgent = $agents[$randomAgent];
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSLVERSION => 6,
        CURLOPT_USERAGENT => $randomAgent
    ]);
    $response = curl_exec($ch);
    if ($response === false)
        exit(curl_error($ch));

    if ($_GET['p'] == 'beta') {
        $weatherData = json_decode($response);
        $outputStr = "<!doctype html>\n";
        $outputStr .= "<html lang=\"en\">\n";
        $outputStr .= "<head>\n";
        $outputStr .= "    <meta charset=\"utf-8\">\n";
        $outputStr .= "    <title>Weather</title>\n";
        $outputStr .= "    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC\" crossorigin=\"anonymous\">\n";
        $outputStr .= "    <style>\n";
        $outputStr .= "        body {\n";
        $outputStr .= "            padding: 20px;\n";
        $outputStr .= "            max-width: 800px;\n";
        $outputStr .= "            margin: auto;\n";
        $outputStr .= "        }\n";
        $outputStr .= "    </style>\n";
        $outputStr .= "   <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p\" crossorigin=\"anonymous\" defer></script>\n";    
        $outputStr .= "</head>\n";
        $outputStr .= "<body>\n";
        $outputStr .= "<table class=\"table\">\n";
        $outputStr .= "    <tbody>\n";
        $periods = $weatherData->properties->periods;
        foreach ($periods as $period) {
                $outputStr .= "        <tr>\n";
                $outputStr .= "            <td>".$period->name."</td>\n";
                $outputStr .= "            <td>".$period->temperature." ".$period->temperatureUnit."</td>\n";
                $outputStr .= "            <td>".$period->relativeHumidity->value."&percnt;</td>\n";
                $outputStr .= "            <td>".$period->windSpeed."</td>\n";
                $outputStr .= "            <td><a data-bs-toggle=\"tooltip\" title=\"".$period->detailedForecast. "\" href=\"javascript:void(0);\">Forecast <i class=\"bi bi-info-circle\"></i></a></td>\n";
                $outputStr .= "        </tr>\n";
        }
        $outputStr .= "    </tbody>\n";
        $outputStr .= "</table>\n";
        $outputStr .= "<script>\n";
        $outputStr .= "document.addEventListener('DOMContentLoaded', () => {\n";
        $outputStr .= "    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'))\n";
        $outputStr .= "    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {\n";
        $outputStr .= "        return new bootstrap.Tooltip(tooltipTriggerEl)\n";
        $outputStr .= "    })\n";
        $outputStr .= "});\n";
        $outputStr .= "</script>\n";
        $outputStr .= "</body>\n";
        $outputStr .= "</html>\n";
        $file = 'weather.html';
        file_put_contents($file, $outputStr);    
    } elseif ($_GET['p'] == 'alpha') {
        header('Content-type: application/json');
        echo $response;
    } elseif ($_GET['p'] == 'gamma') {
        $weatherData = json_decode($response);
        $periods = $weatherData->properties->periods;
        $payload = [];
        foreach ($periods as $period) {
            $payload[] = $period;
        }
        header('Content-type: application/json');
        echo json_encode($payload);
    }
}

