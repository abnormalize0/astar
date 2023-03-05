<?php
define("NOT_VISITED", 0);
define("SEEN", 1);
define("VISITED", 2);

class Maze {
	public $height;
	public $width;
	public $current_x;
	public $current_y;
	public $answer_x;
	public $answer_y;
	public $structure;
	public function __construct($maze_structure) {
		$this->structure = $maze_structure;
		$this->height = count($maze_structure);
		$this->width = count($maze_structure[0]);
	}
	public function validate() {
		if (($this->height == 1) || ($this->width == 1)) {
			return false;
		}
		for ($i = 1;$i < count($this->structure);$i++) {
			if (count($this->structure[$i]) != count($this->structure[0])) {
				return false;
			}
		}
		return true;
	}
	public function convert_coord_to_index($x, $y) {
		return $x * $this->width + $y;
	}
	public function get_cell_weight($x, $y) {
		return $this->structure[$x][$y];
	}
}

class Cell {
	public $x;
	public $y;
	public $id;
	public $previous_x;
	public $previous_y;
	public $state;
	public $from_start;
	public $to_end;
	public $weight;
	public function __construct($x, $y, $weight, $id) {
		$this->x = $x;
		$this->y = $y;
		$this->weight = $weight;
		$this->from_start = 0;
		$this->to_end = 0;
		$this->previous_x = - 1;
		$this->previous_y = - 1;
		$this->state = NOT_VISITED;
		$this->id = $id;
	}
	function astar_step($origin_dist, $seen_cells, $current_x, $current_y, $answer_x, $answer_y) {
		if ($this->state == NOT_VISITED) {
			$this->from_start = $origin_dist + $this->weight;
			$this->to_end = $this->distance_to_point($answer_x, $answer_y);
			$this->previous_x = $current_x;
			$this->previous_y = $current_y;
			$this->state = SEEN;
			array_push($seen_cells, $this->id);
		}
		else if (($this->state == SEEN) && ($this->from_start > $origin_dist + $this->weight)) {
			$this->from_start = $origin_dist + $this->weight;
			$this->previous_x = $current_x;
			$this->previous_y = $current_y;
		}
		return $seen_cells;
	}
	function distance_to_point($x, $y) {
		$deltax = abs($x - $this->x);
		$deltay = abs($y - $this->y);
		return sqrt($deltax**2 + $deltay**2);
	}
}

function set_points($maze) {
	if ($_COOKIE["point1x"] == - 1) {
		return false;
	}
	$maze->current_x = $_COOKIE["point1x"];
	$maze->current_y = $_COOKIE["point1y"];
	$maze->answer_x = $_COOKIE["point2x"];
	$maze->answer_y = $_COOKIE["point2y"];
	return true;
}

function display_table($maze, $path = [], $draw_path = false) {
	echo "<table>" . "\n";
	for ($i = 0;$i < $maze->height;$i++) {
		echo "<tr>" . "\n";
		for ($j = 0;$j < $maze->width;$j++) {
			$id_1d = $maze->convert_coord_to_index($i, $j);
			if (($draw_path) && (array_search($id_1d, $path)) != "") {
				echo "<td><div class='action' style='background-color: #45A29E;' id='cell" . $id_1d . "'";
			}
			else {
				echo "<td><div class='action' id='cell" . $id_1d . "'";
			}
			if ($maze->structure[$i][$j] != 0) {
			    echo " onclick='setpoint(" . $i . ", " . $j . ", " . $id_1d . ")'>";
			} else {
			    echo ">";
			}
			echo ($maze->structure[$i][$j]);
			echo "</div></td>" . "\n";
		}
		echo "</tr>" . "\n";
	}
	echo "</table>" . "\n" . "<br>" . "<br>";
}

function read_file() {
	$maze = array();
	$analyze = 0;
	if (isset($_SESSION['file'])) {
		$analyze = $_SESSION['file'];
	}
	else {
		$myfile = fopen("uploads/" . $_COOKIE["file"], "r");
		$analyze = fread($myfile, filesize("uploads/" . $_COOKIE["file"]));
		fclose($myfile);
		unlink("uploads/" . $_COOKIE["file"]);
		$_SESSION['file'] = $analyze;
	}
	if (!isset($_COOKIE['point1x'])) {
        reset_coordinates();
    }
	if ($analyze == 0) {
		return $maze;
	}
	$rows = explode("\n", $analyze);
	foreach ($rows as & $item) {
		if (count(explode(" ", $item)) != 1) {
			array_push($maze, explode(" ", $item));
		}
	}
	return $maze;
}

function reset_coordinates() {
	setcookie("point1x", "-1", time() + 3600);
	setcookie("point1y", "-1", time() + 3600);
	setcookie("point2x", "-1", time() + 3600);
	setcookie("point2y", "-1", time() + 3600);
}

function astar_process($maze, $cell) {
	$seen_cells = array();
	$rounds = 0;
	while (true) {
	    $rounds++;
		$origin_cell_idx = $maze->convert_coord_to_index($maze->current_x, $maze->current_y);
		if (($maze->current_x > 0) && ($maze->get_cell_weight($maze->current_x - 1, $maze->current_y) != 0)) {
			$neighbor_cell_idx = $maze->convert_coord_to_index($maze->current_x - 1, $maze->current_y);
			$seen_cells = $cell[$neighbor_cell_idx]->astar_step($cell[$origin_cell_idx]->from_start , $seen_cells, $maze->current_x, $maze->current_y, $maze->answer_x, $maze->answer_y);
		}
		if (($maze->current_x < $maze->height - 1) && ($maze->get_cell_weight($maze->current_x + 1, $maze->current_y) != 0)) {
			$neighbor_cell_idx = $maze->convert_coord_to_index($maze->current_x + 1, $maze->current_y);
			$seen_cells = $cell[$neighbor_cell_idx]->astar_step($cell[$origin_cell_idx]->from_start , $seen_cells, $maze->current_x, $maze->current_y, $maze->answer_x, $maze->answer_y);
		}
		if (($maze->current_y > 0) && ($maze->get_cell_weight($maze->current_x, $maze->current_y - 1) != 0)) {
			$neighbor_cell_idx = $maze->convert_coord_to_index($maze->current_x, $maze->current_y - 1);
			$seen_cells = $cell[$neighbor_cell_idx]->astar_step($cell[$origin_cell_idx]->from_start , $seen_cells, $maze->current_x, $maze->current_y, $maze->answer_x, $maze->answer_y);
		}
		if (($maze->current_y < $maze->width - 1) && ($maze->get_cell_weight($maze->current_x, $maze->current_y + 1) != 0)) {
			$neighbor_cell_idx = $maze->convert_coord_to_index($maze->current_x, $maze->current_y + 1);
			$seen_cells = $cell[$neighbor_cell_idx]->astar_step($cell[$origin_cell_idx]->from_start , $seen_cells, $maze->current_x, $maze->current_y, $maze->answer_x, $maze->answer_y);
		}
		if (count($seen_cells) == 0) {
			display_table($maze);
			return;
		}
		$new_best = $seen_cells[0];
		foreach ($seen_cells as &$value) {
			if ($cell[$value]->from_start + $cell[$value]->to_end < $cell[$new_best]->from_start + $cell[$new_best]->to_end) {
				$new_best = $value;
			}
			else if (($cell[$value]->from_start + $cell[$value]->to_end == $cell[$new_best]->from_start + $cell[$new_best]->to_end) && ($cell[$value]->to_end > $cell[$new_best]->to_end)) {
				$new_best = $value;
			}
		}
		$best_id = array_search($new_best, $seen_cells);
		array_splice($seen_cells, $best_id, 1);
		$cell[$origin_cell_idx]->state = VISITED;
		$maze->current_x = $cell[$new_best]->x;
		$maze->current_y = $cell[$new_best]->y;
		if (($maze->current_x == $maze->answer_x) && ($maze->current_y == $maze->answer_y)) {
			$path = array();
			array_push($path, "-1");
			array_push($path, $new_best);
			while ($cell[$new_best]->previous_x != - 1) {
				$new_best = $maze->convert_coord_to_index($cell[$new_best]->previous_x, $cell[$new_best]->previous_y);
				array_push($path, $new_best);
			}
			display_table($maze, $path, true);
			return;
		}
	}
}

function main() {
	session_start();
	$maze_structure = read_file();
	if (count($maze_structure) == 0) {
	    echo("<div class='warning'>Подан неподходящий файл.</div>");
	    return;
	}
	$maze = new Maze($maze_structure);
	if (!$maze->validate()) {
	    echo("<div class='warning'>Подана неправильная матрица.</div>");
		return;
	}
	if (!set_points($maze)) {
		display_table($maze);
		reset_coordinates();
		return;
	}
	reset_coordinates();
	
	$cell = array();
	for ($i = 0;$i < $maze->height;$i++) {
		for ($j = 0;$j < $maze->width;$j++) {
			array_push($cell, new Cell($i, $j, $maze->get_cell_weight($i, $j) , $maze->convert_coord_to_index($i, $j)));
		}
	}
	astar_process($maze, $cell);
}
main();
?>
