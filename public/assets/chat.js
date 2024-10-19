const { createApp } = Vue

createApp({
    data() {
        return {
            base_url: '',
            chat_id: 0,
            messages: [{
                id: 1,
                user_type: 'sender',
                msg_type: 1,
                msg: 'hello',
                date: '12:30 pm',
            },
            {
                id: 2,
                user_type: 'receiver',
                msg_type: 1,
                msg: 'hello',
                date: '12:30 pm',
            },
            {
                id: 3,
                user_type: 'sender',
                msg_type: 1,
                msg: 'hello',
                date: '12:30 pm',
            },
            ],
            archives: [],
            general_chats: [],
            personal_chats: [],
        }
    },
    methods: {
        generateMsg(user_type, msg_type, msg, date) {

            let the_message = ``
            if (msg_type == 1) {
                the_message = ` <div class="the-message">${msg}</div>`;
            } else if (msg_type == 3) {
                the_message = `<div class="the-message"><img class="the-message-img" src="${base_url.value}+${msg}" alt=""></div>`;
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
                        <div class="name-img">اح</div>
                    </div>`;

            }
        },
        getGeneralChats() {
            let this_ = this;
            $.ajax({
                url: base_url.value + 'general-chats',
                type: 'get',
                success: function (data) {
                    this_.general_chats = data.data;
                }
            })
        },
        getPersonalChats() {
            let this_ = this;
            $.ajax({
                url: base_url.value + 'personal-chats',
                type: 'get',
                success: function (data) {
                    this_.personal_chats = data.data;
                }
            })
        },

        getMessages(chat_id) {
            let this_ = this;
            $.ajax({
                url: base_url.value + 'chat-msgs',
                type: 'get',
                data: {
                    chat_id: chat_id
                },
                success: function (data) {
                    this_.messages = data.data;
                }
            })
        },

        getArchivedChats() {
            let this_ = this;
            $.ajax({
                url: base_url.value + 'archived-chats',
                type: 'get',
                success: function (data) {
                    this_.archives = data.data;
                }
            })
        }

    },
    watch: {
        'general_chats': function () {
            console.log(this.general_chats);
        },
        'personal_chats': function () {
            console.log(this.personal_chats);
        },
        'messages': function () {
            console.log(this.messages);
        },
        'archives': function () {
            console.log(this.archives);
        },
        'chat_id': function () {
            console.log(this.chat_id);
        },
        'base_url': function () {
            console.log(this.base_url);
        }
    },
    mounted() {
        this.getGeneralChats();
        this.getPersonalChats();
        this.getArchivedChats();
    }
}).mount('#app')