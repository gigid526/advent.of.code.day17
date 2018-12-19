<?php
$limit = isset($argv[1]) ? $argv[1] : 119;
function stamp($ground) {
    echo trim(array_reduce($ground, function ($carry, $row) { return $carry . PHP_EOL . implode('', $row); }, '')) . PHP_EOL . PHP_EOL;
}
$ground=[];
$flags = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
$bounds = ['x' => [PHP_INT_MAX, PHP_INT_MIN], 'y' => [0, PHP_INT_MIN]];
foreach (file(__DIR__ . '/inputPuzzle.txt') as $line) {
    $matches = [];
    preg_match('/(.)=(\d+), (.)=(\d+)..(\d+)/', $line, $matches);
    if ($bounds[$matches[1]][0] > $matches[2]) $bounds[$matches[1]][0] = $matches[2];
    if ($bounds[$matches[1]][1] < $matches[2]) $bounds[$matches[1]][1] = $matches[2];
    if ($bounds[$matches[3]][0] > $matches[4]) $bounds[$matches[3]][0] = $matches[4];
    if ($bounds[$matches[3]][1] < $matches[5]) $bounds[$matches[3]][1] = $matches[5];
    ${$matches[1]} = $matches[2];
    for (${$matches[3]} = $matches[4]; ${$matches[3]} <= $matches[5]; ++${$matches[3]}) {
        $ground[$y][$x] = '#';
    }
}
$bounds['x'][0]--;
$bounds['x'][1]++;
for ($y = $bounds['y'][0]; $y <= $bounds['y'][1]; ++$y) {
    for ($x = $bounds['x'][0]; $x <= $bounds['x'][1]; ++$x) {
        if (isset($ground[$y][$x]) === false) {
            $ground[$y][$x] = '.';
        }
    }
    ksort($ground[$y]);
}
ksort($ground);

$waterDrops = [[0, 500, 0]];
for ($t = 0; $t < $limit && count($waterDrops); ++$t) {
    $waterDrop = array_shift($waterDrops);
    $ground[$waterDrop[0]][$waterDrop[1]] = '~';
    if (isset($ground[$waterDrop[0] + 1][$waterDrop[1]]) && $ground[$waterDrop[0] + 1][$waterDrop[1]] === '.') {
        $ground[$waterDrop[0]][$waterDrop[1]] = '|';
        if ($waterDrop[0] < $bounds['y'][1]) {
            array_push($waterDrops, [$waterDrop[0] + 1, $waterDrop[1], 0]);
        }
    } else if ($waterDrop[0] < $bounds['y'][1]) {
        if ($waterDrop[2] === 0 && isset($ground[$waterDrop[0] + 1][$waterDrop[1]]) && $ground[$waterDrop[0] + 1][$waterDrop[1]] === '|') {
            $ground[$waterDrop[0]][$waterDrop[1]] = '|';
            continue;
        }
        $isOverflow = false;
        $waterDrops2 = [];
        $right = [$waterDrop[0], $waterDrop[1]];
        while (in_array($ground[$right[0]][$right[1] + 1], ['.', '|', '~'])) {
            $right[1]++;
            if (in_array($ground[$right[0] + 1][$right[1]], ['.', '|'])) {
                array_push($waterDrops2, [$right[0], $right[1], 0]);
                $isOverflow = true;
                break;
            }
        }
        $left = [$waterDrop[0], $waterDrop[1]];
        while (in_array($ground[$left[0]][$left[1] - 1], ['.', '|', '~'])) {
            $left[1]--;
            if (in_array($ground[$left[0] + 1][$left[1]], ['.', '|'])) {
                array_push($waterDrops2, [$left[0], $left[1], 0]);
                $isOverflow = true;
                break;
            }
        }
        if ($isOverflow === false) {
            for ($x = $left[1]; $x <= $right[1]; ++$x) {
                $ground[$waterDrop[0]][$x] = '~';
            }
            array_push($waterDrops, [$waterDrop[0] - 1, $waterDrop[1], 1]);
        } else {
            for ($x = $left[1]; $x <= $right[1]; ++$x) {
                $ground[$waterDrop[0]][$x] = '|';
            }
            $waterDrops = array_merge($waterDrops, $waterDrops2);
        }
    }
    $tabu = [];
    foreach (array_keys($waterDrops) as $idx) {
        $key = $waterDrops[$idx][0] . '_' . $waterDrops[$idx][1];
        if (in_array($key, $tabu)) {
            unset($waterDrops[$idx]);
        } else {
            array_push($tabu, $key);
        }
    }
    $waterDrops = array_slice($waterDrops, 0);
}
stamp($ground);
$result = 0;
$result2 = 0;
for ($y = $bounds['y'][0]; $y <= $bounds['y'][1]; ++$y) {
    for ($x = $bounds['x'][0]; $x <= $bounds['x'][1]; ++$x) {
        if (in_array($ground[$y][$x], ['~', '|'])) {
            $result++;
        }
        if (in_array($ground[$y][$x], ['~'])) {
            $result2++;
        }
    }
}
echo 'RESULT: ' . ($result - 1) . PHP_EOL;
echo 'RESULT2: ' . ($result2) . PHP_EOL;