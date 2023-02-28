<?php
define ("NOT_VISITED", 0);
define ("SEEN", 1);
define ("VISITED", 2);

$height = 5;
$width = 5;
$maze = [
    [1,1,1,1,1],
    [9,1,1,1,1],
    [9,0,0,0,1],
    [9,1,1,1,1],
    [1,1,2,1,1]
];

$current_x = 4;
$current_y = 0;

$answer_x = 0;
$answer_y = 0;

function conv($x, $y) {
	return $y * $GLOBALS["height"] + $x;
}

function break_x($id) {
	return $id % $GLOBALS["height"];
}

function break_y($id) {
	return intdiv($id, $GLOBALS["height"]);
}

function straight_line($x1,$y1,$x2,$y2) {
	$deltax = abs($x1 - $x2);
	$deltay = abs($y1 - $y2);
	$f = 0.0;
	$f = $deltax ** 2 + $deltay ** 2;
	return sqrt($deltax ** 2 + $deltay ** 2);
}

function display($cell, $maze) {
    echo "<table>"."\n";
	for($i = 0; $i < $GLOBALS["height"]; $i++) {
        echo "<tr>"."\n";
		for($j = 0; $j < $GLOBALS["width"]; $j++) {
            echo "<td>"."\n";
			echo($maze[$i][$j])." [";
			echo($cell[conv($i, $j)]['from_start'])." ";
			echo($cell[conv($i, $j)]['to_end'])."] ";
            echo "</td>"."\n";
		}
        echo "</tr>"."\n";
	}
    echo "</table>"."\n"."<br>"."<br>";
}

function pathfinding($cell, $initial) {
    $current_cell = $initial;
    $path = array();
    array_push($path, $current_cell);
    while ($cell[$current_cell]["previous"] != -1) {
        $current_cell = $cell[$current_cell]["previous"];
        array_push($path, $current_cell);
    }
    array_flip($path);
    foreach($path as &$item) {
        echo break_x($item)." ".break_y($item)."<br>";
    }
}

$cell = array();
$seen_cells = array();
for($i = 0; $i < $height * $width; $i++) {
	array_push($cell, array('from_start' => 0, 'to_end' => 0, 'previous' => -1, 'state' => 0));
}

$cycle = 1;
$counter = 0;

while($cycle) {
	if (($current_x > 0)&&($maze[$current_x - 1][$current_y] != 0)) {
		$id = conv($current_x - 1, $current_y);
		if ($cell[$id]['state'] == NOT_VISITED) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x - 1][$current_y];
			$cell[$id]['to_end'] = straight_line($answer_x, $answer_y, $current_x - 1, $current_y);
			$cell[$id]['previous'] = conv($current_x, $current_y);
			$cell[$id]['state'] = SEEN;
			array_push($seen_cells, $id);
		} else if (($cell[$id]['state'] == SEEN)&&($cell[$id]['from_start'] > $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x - 1][$current_y])) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x - 1][$current_y];
			$cell[$id]['previous'] = conv($current_x, $current_y);
		}
	} 
	if (($current_x < $height - 1)&&($maze[$current_x + 1][$current_y] != 0)) {
		$id = conv($current_x + 1, $current_y);
		if ($cell[$id]['state'] == NOT_VISITED) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y];
			$cell[$id]['to_end'] = straight_line($answer_x, $answer_y, $current_x + 1, $current_y);
			$cell[$id]['previous'] = conv($current_x, $current_y);
			$cell[$id]['state'] = SEEN;
			array_push($seen_cells, $id);
		} else if (($cell[$id]['state'] == SEEN)&&($cell[$id]['from_start'] > $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x + 1][$current_y])) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x + 1][$current_y];
			$cell[$id]['previous'] = conv($current_x, $current_y);
		}
	} 
	if (($current_y > 0)&&($maze[$current_x][$current_y - 1] != 0)) {
		$id = conv($current_x, $current_y - 1);
		if ($cell[$id]['state'] == NOT_VISITED) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y - 1];
			$cell[$id]['to_end'] = straight_line($answer_x, $answer_y, $current_x, $current_y - 1);
			$cell[$id]['previous'] = conv($current_x, $current_y);
			$cell[$id]['state'] = SEEN;
			array_push($seen_cells, $id);
		} else if (($cell[$id]['state'] == SEEN)&&($cell[$id]['from_start'] > $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y - 1])) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y - 1];
			$cell[$id]['previous'] = conv($current_x, $current_y);
		}
	} 
	if (($current_y < $width - 1)&&($maze[$current_x][$current_y + 1] != 0)) {
		$id = conv($current_x, $current_y + 1);
		if ($cell[$id]['state'] == NOT_VISITED) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y + 1];
			$cell[$id]['to_end'] = straight_line($answer_x, $answer_y, $current_x, $current_y + 1);
			$cell[$id]['previous'] = conv($current_x, $current_y);
			$cell[$id]['state'] = SEEN;
			array_push($seen_cells, $id);
		} else if (($cell[$id]['state'] == SEEN)&&($cell[$id]['from_start'] > $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y + 1])) {
			$cell[$id]['from_start'] = $cell[conv($current_x, $current_y)]['from_start'] + $maze[$current_x][$current_y + 1];
			$cell[$id]['previous'] = conv($current_x, $current_y);
		}
	} 
	$new_best = $seen_cells[0];
	foreach($seen_cells as &$value) {
		if ($cell[$value]['from_start'] + $cell[$value]['to_end'] < $cell[$new_best]['from_start'] + $cell[$new_best]['to_end']) {
			$new_best = $value;
		} else if (($cell[$value]['from_start'] + $cell[$value]['to_end'] == $cell[$new_best]['from_start'] + $cell[$new_best]['to_end'])&&($cell[$value]['to_end'] < $cell[$new_best]['to_end'])) {
			$new_best = $value;
		}
	}
	$best_id = array_search($new_best, $seen_cells);
	array_splice($seen_cells, $best_id, 1);
    $cell[conv($current_x,$current_y)]['state'] = VISITED;
	$current_x = break_x($new_best);
	$current_y = break_y($new_best);
	echo($current_x)." ";
	echo($current_y)."<br>";
	display($cell, $maze);
	if (($current_x == $answer_x) && ($current_y == $answer_y)) {
		$cycle = 0;
        pathfinding($cell, $new_best);
	}
	$counter++;
	if ($counter > 200) {
		$cycle = 0;
	}
}
    
?>