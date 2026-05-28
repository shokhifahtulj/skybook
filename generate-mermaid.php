<?php

$routes = file('routes.txt');

$mermaid = [];
$mermaid[] = "flowchart TD";
$mermaid[] = "";

foreach ($routes as $line) {

    if (
        strpos($line, 'GET') !== false ||
        strpos($line, 'POST') !== false ||
        strpos($line, 'PUT') !== false ||
        strpos($line, 'DELETE') !== false ||
        strpos($line, 'PATCH') !== false
    ) {

        $parts = preg_split('/\\s+/', trim($line));

        if (count($parts) >= 4) {

            $method = $parts[0];
            $uri = $parts[1];

            $action = end($parts);

            if (strpos($action, '@') !== false) {

                $actionParts = explode('@', $action);

                $controllerFull = $actionParts[0];
                $controllerMethod = $actionParts[1];

                $controllerArray = explode('\\', $controllerFull);

                $controller = end($controllerArray);

                $routeNode = "R" . md5($uri);
                $controllerNode = "C" . md5($controller . $controllerMethod);

                $mermaid[] = "{$routeNode}[\"{$method} {$uri}\"]";
                $mermaid[] = "{$controllerNode}[\"{$controller}@{$controllerMethod}\"]";
                $mermaid[] = "{$routeNode} --> {$controllerNode}";
                $mermaid[] = "";
            }
        }
    }
}

file_put_contents('flowchart.mmd', implode(PHP_EOL, $mermaid));

echo "flowchart.mmd generated";