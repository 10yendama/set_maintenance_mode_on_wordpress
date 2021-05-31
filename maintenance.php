<?php
// 指定時間内をメンテナンス中にするクラス
class Maintenance_schejule
{
        //メンテナンスモード開始時刻+終了時刻を定義する為のメソッド引数
        public $switch;
        public $start;
        public $end;
 
        // 年月日、開始終了時刻のフォーマット用変数
        private $this->time  = time();
        private $this->date  = date("Y/m/d") . " ";
        private $this->start_time;
        private $this->end_time;
 
    // 実際にWPをメンテナンスモードに移行するメソッド
    function set_maintenance_mode($switch == 0, $start, $end)
    {
        // 第一引数が何もない場合、メンテナンスモードに移行しない
        if (empty($switch)!==FALSE)
        {
            return false;
        }
 
        // 開始時刻と終了時刻のフォーマットをチェック、間違っていたらエラーを吐き出して強制終了
        if (preg_match("/^([0-9]{1,2})\:([0-9]{1,2})$/",$start,$this->start_time->fmt)===FALSE)
        {
            exit("Start time format was different.");
        }
        else if (preg_match("/^([0-9]{1,2})\:([0-9]{1,2})$/",$end,$this->end_time->fmt)===FALSE)
        {
            exit("End time format was different.");
        }
 
        // 時と分の値が正しいかチェック
        if ((intval($this->start_time->fmt[1]) < 0) || (intval($this->start_time->fmt[1]) > 24))
        {
            exit("Set hour of start date to 00-24.");
        }
        else if ((intval($this->start_time->fmt[2]) < 0) || (intval($this->start_time->fmt[2]) > 59))
        {
            exit("Set minute of start date to 00-59.");
        }
        else if ((intval($this->end_time->fmt[1]) < 0) || (intval($this->end_time->fmt[1]) > 24))
        {
            exit("Set hour of end date to 00-24.");
        }
        else if ((intval($this->end_time->fmt[2]) < 0) || (intval($this->end_time->fmt[2]) > 59))
        {
            exit("Set minute of end date to 00-59.");
        }
 
        // 開始終了時刻のフォーマットを整える
        $this->start_time->tm = sprintf("%02d",$this->start_time->fmt[1]).":".sprintf("%02d",$this->start_time->fmt[2]);
        $this->end_time->tm   = sprintf("%02d",$this->end_time->fmt[1]).":".sprintf("%02d",$this->end_time->fmt[2]);
 
        // 開始終了時刻をUNIX時間に置換する
        $this->start_time->std = date("U", $this->date . $this->start_time->tm . ":00");
        $this->end_time->std   = date("U", $this->date . $this->end_time->tm . ":00");
 
        // セット時刻が日にちを跨ぐ場合、開始又は終了時刻を1日増減する
        if (($this->time > $this->start_time->std) && ($this->start_time->std >= $this->end_time->std))
        {
            $this->end_time->std += 24 * 60 * 60;
        }
        else if (($this->time < $this->start_time->std) && ($this->time < $this->end_time->std))
        {
            $this->start_time->std -= 24 * 60 * 60;
        }
 
        // メンテナンスモード指定時間内にアクセスされた場合、メンテナンスモードに強制移行し
        // 時間外ではメンテナンスモードを解除する
        if (($this->time >= $this->start_time->std) && ($this->time < $this->end_time->std))
        {
            if (file_exists("./.maintenance")===FALSE)
            {
                $fp = @fopen("./.maintenance","w");
                @fwrite($fp,"<?php $upgrading = time(); ?>");
                @fclose($fp);
            }
        }
        else
        {
            if (file_exists("./.maintenance")!==FALSE)
            {
                @unlink("./.maintenance");
            }
        }
    }
}
 
// 上記クラスを呼び出す
$maintenance_schejule = new Maintenance_schejule();
 
// メンテナンスモードスケジューリングを行うメソッドを叩く
// 引数1:この機能を使う場合は1、使わない場合は0をセットする
// 引数2:メンテナンスモード開始時間
// 引数3:メンテナンスモード終了時間
// 23:00～04:00といったように日にちをまたいでもOK
$maintenance_schejule->set_maintenance_mode(1, "00:00", "06:00");
?>
