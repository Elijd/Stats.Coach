<?php

namespace Model;

use Model\Helpers\GolfRelay;
use Psr\Singleton;
use Modules\Database;

class Golf extends GolfRelay
{
    use Singleton;

    private $db;

    public function __wakeup()
    {
        $this->db = Database::getConnection();  // establish a connection

        foreach (get_object_vars( $this ) as $key => $val)
            $GLOBALS['golf'][$key] = $val;
    }

    public function __sleep()
    {
        return (!empty($this->course_id) ? [
            'course_id', 'created_by', 'course_name', 'created_date', 'course_holes',
            'course_street', 'course_city', 'course_state', 'course_phone', 'course_elevation',
            'course_difficulty', 'course_rank', 'box_color_1', 'box_color_2', 'box_color_3',
            'box_color_4', 'box_color_5', 'par_1', 'par_2', 'par_3', 'par_4', 'par_5',
            'par_6', 'par_7', 'par_8', 'par_9', 'par_out', 'par_10', 'par_11', 'par_12', 'par_13',
            'par_14', 'par_15', 'par_16', 'par_17', 'par_18', 'par_in', 'par_tot', 'par_hcp', 'course_type', 'course_access'] : 0);
    }

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function coursesByState($state)
    {
        $sql = "SELECT course_name, course_id FROM StatsCoach.golf_course WHERE course_state= ?";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( array($state) );
        return $stmt->fetchAll();
    }

    public function courseById($id)
    {
        $sql = 'SELECT * FROM StatsCoach.golf_course WHERE course_id = ?';
        $stmt = $this->db->prepare( $sql );
        $stmt->execute( [$id] );
        $data = $stmt->fetch( \PDO::FETCH_ASSOC );

        foreach ($data as $key => $val)
            $GLOBALS['golf'][$key] = $this->$key = $val;
    }

    public function Golf()
    {
        return true;
    }

    public function PostScore()
    {
        if (!empty($this->newScore)) {

            $score_out=$score_in=$score_tot=0;
            for($i=0;$i<8;$i++) $score_out+=$this->newScore[$i];
            for($i=9;$i<18;$i++) $score_in+=$this->newScore[$i];
            $score_tot = $score_in + $score_out;

            try {
                $sql = "INSERT INTO StatsCoach.golf_rounds (round_public, user_id, course_id, score_1, score_2, score_3, score_4, score_5, score_6, score_7, score_8, score_9, score_out, score_10, score_11, score_12, score_13, score_14, score_15, score_16, score_17, score_18, score_in, score_tot) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $this->db->prepare( $sql );

                if(!$stmt->execute( [
                    1,
                    $this->user['user_id'],
                    $this->course_id,
                    $this->newScore[0],
                    $this->newScore[1],
                    $this->newScore[2],
                    $this->newScore[3],
                    $this->newScore[4],
                    $this->newScore[5],
                    $this->newScore[6],
                    $this->newScore[7],
                    $this->newScore[8],
                    $score_out,
                    $this->newScore[9],
                    $this->newScore[10],
                    $this->newScore[11],
                    $this->newScore[12],
                    $this->newScore[13],
                    $this->newScore[14],
                    $this->newScore[15],
                    $this->newScore[16],
                    $this->newScore[17],
                    $score_in,
                    $score_tot] )) $this->alert['danger'] = "We could not process your request. Please try again.";
                return $this->alert['success'] = "Score successfully added!";
            } catch (\Exception $e) {
                $this->alert['danger'] = $e->getMessage();
            }

        }

        if ($this->course_id != $this->courseId) {
            if ($this->courseId == false) $this->course_id = $this->courseId;
            else $this->courseById( $this->courseId );
        }

        if (!empty($this->boxColor)) {
            $sql = "SELECT * FROM StatsCoach.golf_distance WHERE course_id = ? AND distance_color = ?";
            $stmt = $this->db->prepare( $sql );
            $stmt->execute( [$this->courseId, $this->boxColor] );
            return $this->course_distance_info = $stmt->fetch();
        }

        if (!empty($this->courseId))
            return $this->course_colors = [$this->box_color_1, $this->box_color_2, $this->box_color_3, $this->box_color_4, $this->box_color_5];


        if (!empty($this->state)) {
            $this->courses = $this->coursesByState( $this->state );
            if (empty($this->courses)) $this->courses = true;
        }
    }

    public function AddCourse()
    {
        $holes = &$this->holes;
        $par = &$this->par;
        $tee_boxes = &$this->tee_boxes;
        $teeBox = &$this->teeBox;
        $handicap_number = &$this->handicap_number;
        $handicap = &$this->handicap;
        $course = &$this->course;

        $par = $this->par;
        $par_out = 0;
        $par_in = 0;
        for ($i = 0; $i < 9; $i++) $par_out += $par[$i];
        for ($i = 9; $i < 18; $i++) $par_in += $par[$i];
        $par_tot = $par_out + $par_in;

        $time = time();

        try {
            $sql = "INSERT INTO StatsCoach.golf_course (`created_by`, `created_date`, `course_name`, `course_type`, `course_access`, `course_holes`, `course_street`, `course_city`, `course_state`, `course_phone`, `box_color_1`, `box_color_2`, `box_color_3`, `box_color_4`, `box_color_5`, `par_1`, `par_2`, `par_3`, `par_4`, `par_5`, `par_6`, `par_7`, `par_8`, `par_9`, `par_out`, `par_10`, `par_11`, `par_12`, `par_13`, `par_14`, `par_15`, `par_16`, `par_17`, `par_18`, `par_in`, `par_tot`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if (!$this->db->prepare( $sql )->execute( array(
                $this->user['user_id'],
                $time,
                $course['name'],
                $course['style'],
                $course['access'],
                $holes,
                $course['street'],
                $course['city'],
                $course['state'],
                $course['phone'],
                isset($teeBox[1]) ? $teeBox[1][0] : null,
                isset($teeBox[2]) ? $teeBox[2][0] : null,
                isset($teeBox[3]) ? $teeBox[3][0] : null,
                isset($teeBox[4]) ? $teeBox[4][0] : null,
                isset($teeBox[5]) ? $teeBox[5][0] : null,
                $par[0], $par[1], $par[2], $par[3], $par[4], $par[5], $par[6], $par[7], $par[8], $par_out,
                $par[9], $par[10], $par[11], $par[12], $par[13], $par[14], $par[15], $par[16], $par[17], $par_in, $par_tot) )
            ) throw new \Exception( "Failed inserting courses" );


            $sql = "SELECT StatsCoach.golf_course.course_id FROM StatsCoach.golf_course WHERE StatsCoach.golf_course.created_by AND StatsCoach.golf_course.created_date ";

            $stmt = $this->db->prepare( $sql );
            $stmt->execute( array($this->user['user_id'], $time) );
            $course_id = $stmt->fetch();
            $course_id = $course_id['course_id'];

            if (!$course_id) throw new \Exception( "Sorry, course id lookup failed. Please try again later" );


            $sql = "INSERT INTO StatsCoach.golf_distance (course_id, tee_box, distance_1, distance_2, distance_3, distance_4, distance_5, distance_6, distance_7, distance_8, distance_9, distance_out, distance_10, distance_11, distance_12, distance_13, distance_14, distance_15, distance_16, distance_17, distance_18, distance_in, distance_tot, distance_color)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            for ($i = 1; $i <= $tee_boxes; $i++) {

                $par_in = $par_out = 0;

                for ($j = 1; $j < 9; $j++) $par_out += $teeBox[$i][$j];
                for ($j = 10; $j < 19; $j++) $par_in += $teeBox[$i][$j];
                $par_tot = $par_out + $par_in;

                if (!$this->db->prepare( $sql )->execute( array(
                    $course_id,
                    $i,
                    $teeBox[$i][1],
                    $teeBox[$i][2],
                    $teeBox[$i][3],
                    $teeBox[$i][4],
                    $teeBox[$i][5],
                    $teeBox[$i][6],
                    $teeBox[$i][7],
                    $teeBox[$i][8],
                    $teeBox[$i][9],
                    $par_out,
                    $teeBox[$i][10],
                    $teeBox[$i][11],
                    $teeBox[$i][12],
                    $teeBox[$i][13],
                    $teeBox[$i][14],
                    $teeBox[$i][15],
                    $teeBox[$i][16],
                    $teeBox[$i][17],
                    $teeBox[$i][18],
                    $par_in,
                    $par_tot,
                    $teeBox[$i][0]
                ) )
                ) throw new \Exception( "Failed to insert tee box $i. Critical Error id = $course_id  Please Contact Meh. 817-789-3294" );
            }

            $sql = "INSERT INTO StatsCoach.golf_handicap (course_id, handicap_number, handicap_1, handicap_2, handicap_3, handicap_4, handicap_5, handicap_6, handicap_7, handicap_8, handicap_9, handicap_10, handicap_11, handicap_12, handicap_13, handicap_14, handicap_15, handicap_16, handicap_17, handicap_18)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            for ($i = 1; $i <= $handicap_number; $i++)
                if (!$this->db->prepare( $sql )->execute( array(
                    $course_id,
                    $i,
                    $handicap[$i][0],
                    $handicap[$i][1],
                    $handicap[$i][2],
                    $handicap[$i][3],
                    $handicap[$i][4],
                    $handicap[$i][5],
                    $handicap[$i][6],
                    $handicap[$i][7],
                    $handicap[$i][8],
                    $handicap[$i][9],
                    $handicap[$i][10],
                    $handicap[$i][11],
                    $handicap[$i][12],
                    $handicap[$i][13],
                    $handicap[$i][14],
                    $handicap[$i][15],
                    $handicap[$i][16],
                    $handicap[$i][17]
                ) ) ) throw new \Exception( "Failed to insert tee handicap $i. Critical Error id = $course_id  Please Contact Meh. 817-789-3294" );

            $this->alert['success'] = "The course has been added!";
        } catch (\Exception $e) {
            $this->alert['danger'] = $e->getMessage();
        }

    }

}



