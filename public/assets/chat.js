const { createApp } = Vue

createApp({
    data() {
        return {
            base_url: '',
            msg: '',
            ms_id: 0,
            is_closed: 1,
            is_rated: 0,
            chat_id: 0,
            merchant_id: 0,
            merchant_name: '',
            merchant_username: '',
            messages: [],
            archives: [],
            general_chats: [],
            personal_chats: [],
        }
    },
    methods: {
        getGeneralChats() {
            let this_ = this;
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url.value + 'general-chats',
                    type: 'get',
                    success: function (data) {
                        this_.general_chats = data.data.reverse();
                        resolve();
                    },
                    error: function (err) {
                        if (err.status == 401) {
                            window.location.href = this_.base_url + "logout";
                        }
                        reject(err);
                    }
                })
            });
        },
        getPersonalChats() {
            let this_ = this;
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url.value + 'personal-chats',
                    type: 'get',
                    success: function (data) {
                        this_.personal_chats = data.data.reverse();
                        resolve();
                    },
                    error: function (err) {
                        if (err.status == 401) {
                            window.location.href = this_.base_url + "logout";
                        }
                        reject(err);
                    }
                })
            });
        },
        getArchivedChats() {
            let this_ = this;
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url.value + 'archived-chats',
                    type: 'get',
                    success: function (data) {
                        this_.archives = data.data.reverse();
                        resolve();
                    },
                    error: function (err) {
                        if (err.status == 401) {
                            window.location.href = this_.base_url + "logout";
                        }
                        reject(err);
                    }
                })
            });
        },
        openChat(chat_id, merchant_id, merchant_username, merchant_name, ms_id, is_closed, is_rated) {
            let this_ = this;
            this_.messages = [];
            this_.chat_id = chat_id;
            this_.merchant_id = merchant_id;
            this_.merchant_name = merchant_name;
            this_.merchant_username = merchant_username;
            this_.ms_id = ms_id;
            this_.is_closed = is_closed;
            this_.is_rated = is_rated;

            function recursiveUpdate() {
                this_.updateMessages(chat_id).then(() => {
                    if (chat_id == this_.chat_id) {
                        setTimeout(() => {
                            recursiveUpdate();
                        }, 5000);
                    }
                });
            }
            recursiveUpdate();
        },
        updateMessages(chat_id) {
            let this_ = this;
            return new Promise((resolve, reject) => {
                if (chat_id != this_.chat_id) {
                    reject("Chat ID does not match");
                    return;
                }
                $.ajax({
                    url: base_url.value + 'chat-msgs',
                    type: 'get',
                    data: {
                        chat_id: this_.chat_id
                    },
                    success: function (data) {
                        if (chat_id != this_.chat_id) {
                            reject("Chat ID does not match");
                            return;
                        }
                        this_.messages = [];
                        data.data.forEach(element => {
                            this_.messages.push({
                                id: element.id,
                                user_type: element.reply_from == "merchant" ? "receiver" : "sender",
                                msg_type: element.reply_type,
                                msg: element.reply,
                                date: element.created_at,
                            });
                        });
                        resolve();
                    },
                    error: function (err) {
                        if (err.status == 401) {
                            window.location.href = this_.base_url + "logout";
                        }
                        reject(err);
                    }
                })
            });
        },
        exitChat() {
            this.chat_id = 0;
            this.merchant_id = 0;
            this.merchant_name = '';
            this.merchant_username = '';
            this.ms_id = 0;
            this.messages = [];

        },
        startChat(){
            let this_ = this;
            $.ajax({
                url: base_url.value + 'start-chat?chat_id=' + this_.chat_id,
                type: 'get',
                success: function (data) {
                    this_.ms_id = data.data.ms_id;
                    swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.msg
                    })
                },
                error: function (err) {
                    if (err.status == 401) {
                        window.location.href = this_.base_url + "logout";
                    }
                    let res = JSON.parse(err.responseText);
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: res.msg
                    })
                }
            })
        },
        closeChat() {
            let this_ = this;
            swal.fire({
                title: 'هل انت متاكد من اغلاق الدردشة?',
                text: "سيتم اشعار التاجر بغلق المحادثه",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url.value + 'close-chat?chat_id=' + this_.chat_id,
                        type: 'get',
                        success: function (data) {
                            this_.is_closed = 1;
                            swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.msg
                            })
                        },
                        error: function (err) {
                            if (err.status == 401) {
                                window.location.href = this_.base_url + "logout";
                            }
                            let res = JSON.parse(err.responseText);
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.msg
                            })
                        }
                    })
                }
            })
        },
        reopenChat() {
            let this_ = this;
            swal.fire({
                title: 'هل انت متاكد ?',
                text: "من اعادة فتح الدردشة",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url.value + 'reopen-chat?chat_id=' + this_.chat_id,
                        type: 'get',
                        success: function (data) {
                            this_.is_closed = 0;
                            swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.msg
                            })
                        },
                        error: function (err) {
                            if (err.status == 401) {
                                window.location.href = this_.base_url + "logout";
                            }
                            let res = JSON.parse(err.responseText);
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.msg
                            })
                        }
                    })
                }
            })
        },
        sendMsg() {
            let this_ = this;
            if (this_.chat_id == 0 || this_.msg == '') {
                return;
            }
            let msg = this_.msg;
            this_.messages.unshift({
                id: 0,
                user_type: 'sender',
                msg_type: 1,
                msg: this_.msg,
                date: new Date()
            });
            this_.msg = '';

            $.ajax({
                url: base_url.value + 'send-msg',
                type: 'post',
                data: {
                    chat_id: this_.chat_id,
                    msg: msg,
                    ms_id: this_.ms_id
                },
                success: function (data) {
                    this_.updateMessages(this_.chat_id);
                },
                error: function (err) {
                    if (err.status == 401) {
                        window.location.href = this_.base_url + "logout";
                    }
                    let res = JSON.parse(err.responseText);
                    swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: res.msg
                    })
                }
            })

        },
        sendImg() {
            let this_ = this;
            swal.fire({
                title: 'هل انت متاكد ?',
                text: "ارسال صورة",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'لا'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('chat_id', this_.chat_id);
                    formData.append('img', $('input[name="image_to_upload"]')[0].files[0]);
                    $.ajax({
                        url: base_url.value + 'send-img',
                        type: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            this_.updateMessages(this_.chat_id);
                        },
                        error: function (err) {
                            if (err.status == 401) {
                                window.location.href = this_.base_url + "logout";
                            }
                            let res = JSON.parse(err.responseText);
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: res.msg
                            })
                        }
                    })
                }
            })
        },
        generateMsg(user_type, msg_type, msg, date) {

            let the_message = ``
            if (msg_type == 1) {
                the_message = ` <div class="the-message">${msg}</div>`;
            } else if (msg_type == 3) {
                the_message = `<div class="the-message"><img class="the-message-img" src="${base_url.value+msg}" alt=""></div>`;
            }

            if (user_type == "sender") {
                return `
                    <div class="user-img">
                        <img class="my_img" src="${base_url.value}assets/img/icon/person.svg" alt="">
                    </div>
                    <div class="user-message">
                        ${the_message}
                        <div class="date">${date}</div>
                    </div>`;
            } else {
                return `
                    <div class="user-message">
                        ${the_message}
                        <div class="date">${date}</div>
                    </div>
                    <div class="user-img">
                        <div class="name-img">${this.merchant_name.substr(0, 2)}</div>
                    </div>`;

            }
        },
        getData() {
            let this_ = this;
            function recursiveGet() {
                Promise.all([
                    this_.getGeneralChats(),
                    this_.getPersonalChats(),
                    this_.getArchivedChats()
                ]).then(() => {
                    setTimeout(() => {
                        recursiveGet();
                    }, 10000);
                });
            }
            recursiveGet();
        }
    },
    watch: {
        // 'general_chats': function () {
        //     console.log(this.general_chats);
        // },
        // 'personal_chats': function () {
        //     console.log(this.personal_chats);
        // },
        'messages': function () {
            console.log(this.messages);
        },
        // 'archives': function () {
        //     console.log(this.archives);
        // },
        // 'chat_id': function () {
        //     console.log(this.chat_id);
        // },
        // 'base_url': function () {
        //     console.log(this.base_url);
        // },
        // 'ms_id': function () {
        //     console.log(this.ms_id);
        // },
        'msg' : function () {
            console.log(this.msg);
        },
        'is_closed' : function () {
            console.log(this.is_closed);
        }
    },
    mounted() {
        this.getData();
    }
}).mount('#app')