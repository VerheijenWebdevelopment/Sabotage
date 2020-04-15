<template>
    <div id="chat-online-users" class="elevation-1">

        <!-- Header -->
        <div id="chat-online-users__header">
            <div id="header-icon">
                <i class="fas fa-users"></i>
            </div>
            <div id="header-title">{{ titleText }}</div>
        </div>

        <!-- Content -->
        <div id="chat-online-users__content">

            <!-- Online users -->
            <div id="online-users" v-if="users.length > 0">
                <div class="user" v-for="(user, ui) in users" :key="ui">
                    <div class="user-avatar" :style="{ backgroundImage: 'url('+user.avatar_url+')' }"></div>
                    <div class="user-name">{{ user.username }}</div>
                </div>
            </div>

            <!-- No online users -->
            <div id="no-online-users" v-if="users.length === 0">
                {{ noRecordsText }}
            </div>

        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "titleText",
            "noRecordsText",
        ],
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
                    
                    // When we succesfully connect to the channel
                    .here(function(users) {
                        console.log(this.tag+" Connected to the 'online' channel");
                        console.log("Users currently in 'Online' channel: ", users);
                        this.users = users;
                    }.bind(this))

                    // When a user joins the channel
                    .joining(function(user) {
                        console.log(this.tag+" User joining 'Online' channel: ", user);
                        this.users.push(user);
                        this.$toasted.show(user.username+" is online!", { duration: 3000 });
                    }.bind(this))
                    
                    // When a user leaves the channel
                    .leaving(function(user) {
                        console.log(this.tag+" User leaving 'Online' channel: ", user);
                        let index = this.getUserIndexById(user.id);
                        this.users.splice(index, 1);
                        this.$toasted.show(user.username+" is offline", { duration: 3000 });
                    }.bind(this));

            },
            getUserIndexById(id) {
                for (let i = 0; i < this.users.length; i++) {
                    if (this.users[i].id === id) {
                        return i;
                    }
                }
                return false;
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
            display: flex;
            padding: 15px;
            align-items: center;
            flex-direction: row;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            #header-icon {
                font-size: 1.5em;
                margin: 0 15px 0 0;
            }
            #header-title {
                font-size: 1.2em;
                font-weight: 500;
            }
        }
        #chat-online-users__content {
            #online-users {
                .user {
                    display: flex;
                    padding: 15px;
                    flex-direction: row;
                    box-sizing: border-box;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                    &:last-child {
                        border-bottom: 0;
                    }
                    .user-avatar {
                        height: 40px;
                        flex: 0 0 40px;
                        margin: 0 15px 0 0;
                        border-radius: 20px;
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-color: hsl(0, 0%, 80%);
                    }
                    .user-name {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                    }
                }
            }
            #no-online-users {
                padding: 15px 25px;
                box-sizing: border-box;
            }
        }
    }
</style>