<template>
    <div id="chat-messages" class="elevation-1">
        
        <!-- Header -->
        <div id="chat-messages__header">
            <div id="header-title">Chatbox</div>
        </div>

        <!-- Content -->
        <div id="chat-messages__content">

            <!-- Messages -->
            <div class="chat-message__wrapper" v-for="(message, mi) in messages.slice().reverse()" :key="mi">
                <div class="chat-message">
                    <div class="chat-message__author" v-if="message.user === null">You</div>
                    <div class="chat-message__author" v-if="message.user !== null">{{ message.user }}</div>
                    <div class="chat-message__text">{{ message.text }}</div>
                </div>
            </div>

            <!-- No messages -->
            <div id="no-messages" v-if="messages.length === 0">
                No messages received yet
            </div>

        </div>

        <!-- Send message form -->
        <div id="chat-messages__form">
            <div id="chat-messages__form-left">
                <input type="text" id="chat-messages__form-input" v-model="form.message" placeholder="..." @keydown.enter="onClickSend">
            </div>
            <div id="chat-messages__form-right">
                <v-btn small text color="primary" @click="onClickSend" :disabled="sendButtonDisabled">
                    Send message
                </v-btn>
            </div>
        </div>
        
    </div>
</template>

<script>
    export default {
        props: [
            "sendMessageApiEndpoint",
        ],
        data: () => ({
            tag: "[chat-messages]",
            messages: [],
            form: {
                message: "",
            }
        }),
        computed: {
            sendButtonDisabled() {
                return this.form.message === "";
            },
        },
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" send message api endpoint: ", this.sendMessageApiEndpoint);
                this.startListening();
            },
            onClickSend() {
                console.log(this.tag+" clicked send message button");
                
                // Extract the message and immediately reset it (so it feels like it's instant)
                let message = this.form.message;
                this.form.message = "";

                // Compose a payload
                let payload = new FormData();
                payload.append("message", this.form.message);
                
                // Send API request
                this.axios.post(this.sendMessageApiEndpoint, payload)

                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded", response.data);
                        
                        // Add your own message to the chatbox (as we won't be receiving an event ourselves)
                        this.messages.push({ user: null, text: this.form.message });

                    }.bind(this))

                    // Request failed
                    .catch(function(error) {
                        console.warn(this.tag+" request failed: ", error);
                    }.bind(this));
                
            },
            startListening() {

                // Listen on the chat channel
                Echo.private('chat')

                    // Message received
                    .listen('Chat\\MessageSent', function(e) {
                        console.log(this.tag+" received 'Chat\\MessageSent' event", e);

                        // Add message to the list of messages
                        this.messages.push({ user: e.user.username, text: e.message });

                    }.bind(this));

            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #chat-messages {
        border-radius: 3px;
        background-color: #ffffff;
        #chat-messages__header {
            padding: 15px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            #header-title {
                font-size: 1.2em;
                font-weight: 500;
            }
        }
        #chat-messages__content {
            padding: 25px;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            .chat-message__wrapper {
                margin: 0 0 15px 0;
                &:last-child {
                    margin: 0;
                }
                .chat-message {
                    display: flex;
                    flex-direction: row;
                    .chat-message__author {
                        color: #fff;
                        font-size: .8em;
                        padding: 3px 5px;
                        border-radius: 3px;
                        margin: 0 15px 0 0;
                        box-sizing: border-box;
                        background-color: #333;
                    }
                    .chat-message__text {
                        flex: 1;
                    }
                }
            }
            #no-messages {

            }
        }
        #chat-messages__form {
            display: flex;
            padding: 15px;
            flex-direction: row;
            box-sizing: border-box;
            #chat-messages__form-left {
                flex: 1;
                display: flex;
                flex-direction: row;
                align-items: center;
                #chat-messages__form-input {
                    width: 100%;
                    padding: 8px 10px;
                    box-sizing: border-box;
                    background-color: hsl(0, 0%, 98%);
                }
            }
            #chat-messages__form-right {
                display: flex;
                margin: 0 0 0 15px;
                flex-direction: row;
                align-items: center;
            }
        }
    }
</style>