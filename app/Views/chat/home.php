<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/style.css">
    <link rel="icon" href="<?php echo base_url(); ?>assets/img/WASET.png">
    <title>الرسائل</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>

<body>
    <div id="app">

        <div class="messages-content">
            <!-- archaves sidebar content -->
            <div class="archives">
                <div class="part" id="close-archives-part">
                    <img src="<?php echo base_url(); ?>assets/img/icon/close.svg" alt="">
                </div>
                <div class="part">
                    <div class="main-title">الارشيف</div>
                </div>
                <div class="part">
                    <div class="call-ul">
                        <div class="message-li" v-for="(archive,index) in archives" :key="archive.id" @click="openChat(archive.id,archive.merchant_id,archive.merchant_username,archive.merchant_name,archive.ms_id,archive.is_closed,archive.is_rated)">
                            <div class="img-icon">
                                <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                            </div>
                            <div class="name">
                                <div class="main">{{ archive.merchant_name }} ({{archive.merchant_username}})</div>
                                <div class="sup">
                                    <span>{{ archive.last_reply_type == 3 ? 'صورة' : (archive.last_reply.length > 10 ? archive.last_reply.substr(0, 15) + '...' : archive.last_reply) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end archaves sidebar content -->

            <!-- personal sidebar content -->
            <div class="personal">
                <div class="part" id="close-personal-part">
                    <img src="<?php echo base_url(); ?>assets/img/icon/close.svg" alt="">
                </div>
                <div class="part">
                    <div class="main-title">المعلومات الشخصية</div>
                </div>
                <div class="part">
                    <div class="personal-ul">
                        <div class="upper">
                            <div class="p-li img">
                                <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                            </div>
                            <div class="p-li name">
                                <div class="p-name"><?= session()->get('name'); ?></div>
                                <!-- <div class="re-write">
                                    <img src="<?php //echo base_url(); 
                                                ?>assets/img/icon/pen.svg" alt="">
                                </div> -->
                            </div>
                            <div class="p-li num">
                                <div class="p-num">
                                    <span class="opacity">رقم الهاتف</span>
                                    <span class="info"><?= session()->get('mobile'); ?></span>
                                </div>
                                <!-- <div class="re-write">
                                    <img src="<?php //echo base_url(); 
                                                ?>assets/img/icon/pen.svg" alt="">
                                </div> -->
                            </div>
                            <div class="p-li num">
                                <div class="p-num">
                                    <span class="opacity">رمز الدخول</span>
                                    <span class="info">*********</span>
                                </div>
                                <!-- <div class="re-write">
                                    <img src="<?php //echo base_url(); 
                                                ?>assets/img/icon/pen.svg" alt="">
                                </div> -->
                            </div>
                        </div>
                        <div class="under">
                            <a class="logout-btn" href="<?= base_url('logout'); ?>">تسجيل خروج</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end personal sidebar content -->

            <!-- waseet logo in front of the page  -->
            <div class="message-header">
                <a href=""><img src="<?php echo base_url(); ?>assets/img/whitelogo.svg" alt=""></a>
            </div>

            <div class="message-content">
                <!-- menu sidebar list -->
                <div class="div icons-nav">
                    <div class="top-icon">
                        <div class="tap" id="toggle" class="toggle">
                            <img src="<?php echo base_url(); ?>assets/img/icon/menu.svg" alt="">
                            <span>القائمة</span>
                        </div>
                        <div class="tap" id="message">
                            <img src="<?php echo base_url(); ?>assets/img/icon/message.svg" alt="">
                            <span>الرسائل المخصصة</span>
                        </div>
                    </div>
                    <div class="bottom-icon">
                        <div class="tap" id="archives">
                            <img src="<?php echo base_url(); ?>assets/img/icon/box.svg" alt="">
                            <span>الارشيف</span>
                        </div>
                        <div class="line"></div>
                        <div class="tap" id="personal">
                            <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                            <span>المعلومات الشخصية</span>
                        </div>
                    </div>
                </div>
                <!-- end menu sidebar list -->

                <!-- chats list in sidebar  (general , personal)-->
                <div class="div messages" id="message-type">
                    <div class="close" id="close-message"><img src="<?php echo base_url(); ?>assets/img/icon/close.svg" alt=""></div>
                    <div class="my-messages" id="my-messages">الرسائل </div>
                    <div class="message-box hide">
                        <div class="title" id="message1">
                            <span> الرسائل </span>
                            <span> الرسائل المخصصة </span>
                        </div>
                        <div class="messages-list">

                            <div class="message-li" v-for="(chat,index) in general_chats" :key="chat.id" @click="openChat(chat.id,chat.merchant_id,chat.merchant_username,chat.merchant_name,chat.ms_id,chat.is_closed,chat.is_rated)">
                                <div class="img-icon">
                                    <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                                    <div class="red-dot"></div>
                                </div>
                                <div class="name">
                                    <div class="main">{{ chat.merchant_name }} ({{chat.merchant_username}})</div>
                                    <div class="sup">{{ chat.last_reply_type == 3 ? 'صورة' : (chat.last_reply.length > 10 ? chat.last_reply.substr(0, 15) + '...' : chat.last_reply) }}</div>
                                </div>
                                <div class="time">{{ chat.updated_at }}</div>
                                <div class="notifcation">
                                    <div class="num">
                                        <div class="t">{{chat.last_reply_from == 'merchant' ? 1 : ''}}</div>
                                        <img src="<?php echo base_url(); ?>assets/img/icon/play.svg" alt="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="message-box show">
                        <div class="title" id="message2">
                            <span> الرسائل </span>
                            <span> الرسائل المخصصة </span>
                        </div>
                        <div class="messages-list">
                            <div class="message-li" v-for="(chat,index) in personal_chats" :key="chat.id" @click="openChat(chat.id,chat.merchant_id,chat.merchant_username,chat.merchant_name,chat.ms_id,chat.is_closed,chat.is_rated)">
                                <div class="img-icon">
                                    <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                                    <div class="red-dot"></div>
                                </div>
                                <div class="name">
                                    <div class="main">{{ chat.merchant_name }} ({{chat.merchant_username}})</div>
                                    <div class="sup">{{ chat.last_reply_type == 3 ? 'صورة' : (chat.last_reply.length > 10 ? chat.last_reply.substr(0, 15) + '...' : chat.last_reply) }}</div>
                                </div>
                                <div class="time">{{ chat.updated_at }}</div>
                                <div class="notifcation">
                                    <div class="num">
                                        <div class="t">{{chat.last_reply_from == 'merchant' ? 1 : ''}}</div>
                                        <img src="<?php echo base_url(); ?>assets/img/icon/play.svg" alt="">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end chats list in sidebar  (general , personal)-->

                <div class="div messages-window loading">
                    <!-- no chat selected alert -->
                    <div class="content-img-loader" v-if="chat_id ==0">
                        <img src="<?php echo base_url();
                                    ?>assets/img/WASET.png" alt="">
                        <div class="loader-h">الدردشة الفورية</div>
                        <div class="loader">
                            يتيح التطبيق للمستخدمين إرسال الرسائل النصية والصوتية والفيديو بشكل فوري، مما يجعل
                            التواصل أسرع وأكثر فعالية.
                        </div>
                    </div>
                    <!-- end no chat selected alert -->

                    <!-- chat -->
                    <div class="hrader" v-if="chat_id > 0">
                        <div class="user-part">
                            <div class="user-img">
                                <img src="<?php echo base_url(); ?>assets/img/icon/person.svg" alt="">
                            </div>
                            <div class="user-name">
                                <div class="name">{{ merchant_name }} ({{merchant_username}})</div>
                                <div class="user-id">#{{ chat_id }}</div>
                            </div>
                        </div>
                        <div class="icon-part">
                        </div>
                    </div>
                    <div class="body" v-if="chat_id > 0">
                        <div v-for="(message,index) in messages" :key="message.id" :class="{'sender': message.user_type == 'sender', 'receiver': message.user_type == 'receiver'}" v-html="generateMsg(message.user_type, message.msg_type, message.msg, message.date)">

                        </div>

                    </div>
                    <div class="note-box-btns" v-if="chat_id > 0 && ms_id == 0">
                        <div class="close-note" id="close-note">
                            <img src="<?php echo base_url(); ?>assets/img/icon/close.svg" alt="">
                        </div>
                        <div class="note">هل تريد الانظمام الى المحادثة</div>
                        <div class="btns">
                            <button class="btn" @click="startChat()">انظم الى المحادثة</button>
                            <button class="btn" @click="exitChat()">الخروج من المحادثة</button>
                        </div>
                    </div>

                    <div class="note-box-btns" v-if="chat_id > 0 && ms_id != 0 && is_closed == 1 && is_rated == 0">
                       
                        <div class="note">تم اغلاق هذه المحادثة من قبلك وفي انتضار تقييم العميل</div>
                        <div class="btns">
                            <button class="btn" @click="reopenChat()">اعادة فتح المحادثة</button>
                            <button class="btn" @click="exitChat()">الخروج من المحادثة</button>
                        </div>
                    </div>
                    <div class="footer" v-if="chat_id > 0 && ms_id != 0  && is_closed == 0">
                        <div class="border">
                            <div class="input-message">
                                <input type="text" name="msg" id="msg" placeholder="ادخل نص الرسالة ..." v-model="msg" @keydown.enter="sendMsg()">
                            </div>
                            <div class="input-icons">
                                <button class="in-ic" @click="sendMsg()">
                                    <img src="<?php echo base_url(); ?>assets/img/icon/rectangle.svg" alt="">
                                </button>
                                <div class="in-ic" id="upload">
                                    <img src="<?php echo base_url(); ?>assets/img/icon/clip-icon.svg" alt="">
                                </div>
                            </div>
                            <div class="uplad-img" id="uplad-img">
                                <div class="icon-line">
                                    <div class="icon-tap">
                                        <img src="<?php echo base_url(); ?>assets/img/icon/galory.svg" alt="">
                                        <input type="file" name="image_to_upload" id="image_to_upload" @change="sendImg()"/>
                                    </div>
                                    <!-- <div class="icon-tap">
                                        <img src="<?php //echo base_url(); ?>assets/img/icon/camera-photo.svg" alt="">
                                        <input type="file" />
                                    </div>
                                    <div class="icon-tap">
                                        <img src="<?php //echo base_url(); ?>assets/img/icon/location-icon.svg" alt="">
                                        <input type="file" />
                                    </div>
                                    <div class="icon-tap">
                                        <img src="<?php //echo base_url(); ?>assets/img/icon/contact.svg" alt="">
                                        <input type="file" />
                                    </div> -->
                                    <button class="icon-tap" @click="closeChat()">
                                        <img src="<?php echo base_url(); ?>assets/img/icon/document.svg" alt="">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end chat -->
                </div>
            </div>
            <!-- footer -->
            <div class="message-footer">
                <a href="https://maps.app.goo.gl/Q3h7s56bWB8hJ4wQ6" class="footer-icon">
                    <img src="<?= base_url() ?>assets/img/icon/location.svg" alt="">
                </a>
                <a href="mailto:info@al-waseet.com" class="footer-icon">
                    <img src="<?= base_url() ?>assets/img/icon/email.svg" alt="">
                </a>
                <a href="https://www.instagram.com/alwaseetcompany1" class="footer-icon">
                    <img src="<?= base_url() ?>assets/img/icon/instagram.svg" alt="">
                </a>
                <a href="https://web.facebook.com/alwaseetcompany1?_rdc=1&_rdr" class="footer-icon">
                    <img src="<?= base_url() ?>assets/img/icon/facebook.svg" alt="">
                </a>
            </div>
            <!-- end footer -->
        </div>

        <!-- modal to display the image in the chat -->
        <div class="img-container">
            <div class="close-img-container" id="close-img-container">
                <img src="<?php echo base_url(); ?>assets/img/icon/close.svg" alt="">
            </div>
            <div class="show-img-box" id="show-img-box">
                <img src="" id="imageshow" alt="">
            </div>
        </div>
        <!-- end modal to display the image in the chat -->

    </div>
    <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>" v-model="base_url">
    <script src="<?php echo base_url(); ?>assets/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo base_url(); ?>assets/chat.js"></script>
    <script src="<?php echo base_url(); ?>assets/app.js"></script>
</body>

</html>