<template>
    <div id="chat-online-users" class="elevation-1">

        <!-- Header -->
        <div id="chat-online-users__header">
            <div id="header-title">Online users</div>
        </div>

        <!-- Content -->
        <div id="chat-online-users__content">

            <!-- Online users -->
            <div id="online-users" v-if="users.length > 0">
                <div class="user" v-for="(user, ui) in users" :key="ui">
                    {{ user }}
                </div>
            </div>

            <!-- No online users -->
            <div id="no-online-users" v-if="users.length === 0">
                There are no users online
            </div>

        </div>

    </div>
</template>

<script>
    export default {
        props: [],
        data: () => ({
            tag: "[chat-online-users]",
            users: [],
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                this.startListening();
            },
            startListening() {
                console.log(this.tag+" Started listening for events");
                
                // Online presence channel
                Echo.join('online')
                    .here(function(users) {
                        console.log(this.tag+" Connected to the 'online' channel");
                        console.log("Users currently in 'Online' channel: ", users);
                        this.users = users;
                    }.bind(this))
                    .joining(function(user) {
                        console.log(this.tag+" User joining 'Online' channel: ", user);
                        this.users.push(user);
                    }.bind(this))
                    .leaving(function(user) {
                        console.log(this.tag+" User leaving 'Online' channel: ", user);
                    }.bind(this));

            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #chat-online-users {
        border-radius: 3px;
        background-color: #fff;
        #chat-online-users__header {
            padding: 15px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            #header-title {
                font-size: 1.2em;
                font-weight: 500;
            }
        }
        #chat-online-users__content {
            #online-users {
                .user {
                    padding: 15px 25px;
                    box-sizing: border-box;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                    &:last-child {
                        border-bottom: 0;
                    }
                }
            }
            #no-online-users {
                padding: 15px 25px;
                text-align: center;
                box-sizing: border-box;
            }
        }
    }
</style>