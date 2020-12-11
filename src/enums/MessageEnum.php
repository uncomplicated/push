<?php
/**
 * @author zhangzhenjiang
 */

/**
 * Created by .
 * User: Administrator
 * Date: 2020/9/3
 * Time: 17:27
 */


namespace ggss\push\enums;

class MessageEnum
{


    const MESSAGE_PUSH_TYPE_UNWANTED = 0;//不需要推送
    const MESSAGE_PUSH_TYPE_SINGLE = 1;//单推
    const MESSAGE_PUSH_TYPE_BATCH = 2;//批量推
    const MESSAGE_PUSH_TYPE_TO_APP = 3;//群推

    const MESSAGE_PUSH_STATUS_DEFAULT = 0 ;//未推送
    const MESSAGE_PUSH_STATUS_SUCCESS = 1;//已推送
    const MESSAGE_PUSH_STATUS_ONGOING = 2;//推送中
    const MESSAGE_PUSH_STATUS_ERROR = 3;//推送失败


}