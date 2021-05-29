<?php

namespace App\Jobs;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateVideoUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;
    protected $type; // 1-communities

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 5;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 180;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    //public $retryAfter = 3;

    /**
     * UpdateVideoUrl constructor.
     * @param $video
     * @param $type
     * @param string $delay 测试期间 60秒  上线180-300
     */
    public function __construct($video, $type, $delay = "60")
    {
        $this->video = $video;
        $this->type = $type;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // 视频直链
            $url = get_vimeo_mp4($this->video);
            if ($url && $this->type == 1) {
                Community::where('video', '=', $this->video)->update(['video_url' => $url]);
            }


            // 视频截图
           /* $thumb = get_vimeo_thumb($this->video);
            if ($thumb && $this->type == 1) {
                Teacher::where('video', '=', $this->video)->update(['video_thumb' => $thumb]);
            }*/

        } catch (Exception $exception) {
            $this->release($this->attempts() * 5);
        }
    }
}
