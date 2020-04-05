<template>
    <div id="game-chat">

        <!-- Messages -->
        <div id="game-chat__messages">
            <div class="game-chat__message" v-for="(message, mi) in mutableMessages" :key="mi" :class="{ system: message.author === 'system' }">
                <div class="message-author">
                    <div class="message-author__pill">{{ message.author }}</div>
                </div>
                <div class="message-text">{{ message.message }}</div>
            </div>
        </div>
        
        <!-- Form -->
        <div id="game-chat__form">
            <input type="text" id="game-chat__input" placeholder="..." v-model="form.message" @keypress.enter="onPressEnter">
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "player",
            "sendMessageApiEndpoint",
        ],
        data: () => ({
            tag: "[game-chat]",
            mutableMessages: [],
            form: {
                message: "",
            }
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" player: ", this.player);
                console.log(this.tag+" send message api endpoint: ", this.sendMessageApiEndpoint);
                this.initializeData();
                this.startListening();
            },
            initializeData() {
                this.mutableMessages.push({
                    author: "system",
                    message: "Welcome to the game!"
                });
            },
            startListening() {
                Echo.private("game."+this.game.id)
                    .listen("Game\\GameMessageSent", this.onMessageReceived);
            },
            onMessageReceived(e) {
                console.log(this.tag+" received message", e);
                let username = this.getUsernameByPlayerId(e.message.player_id);
                this.mutableMessages.push({
                    author: username,
                    message: e.message.message,
                });
            },
            onPressEnter() {
                console.log(this.tag+" pressed enter");

                // If something was typed in the input
                if (this.form.message !== "") {

                    // Grab the message before it's gone; poof
                    let message = this.form.message;

                    // Add the message to the list of messages
                    this.mutableMessages.push({
                        author: "You",
                        message: message,
                    });

                    // Compose payload for API request
                    let payload = new FormData();
                    payload.append("game_id", this.game.id);
                    payload.append("message", message);

                    // Poof !!
                    this.form.message = "";

                    // Send API request
                    this.axios.post(this.sendMessageApiEndpoint, payload)

                        // If the request succeeded, hurai
                        .then(function(response) {
                            if (response.data.status === "success") {
                                console.log(this.tag+" operation succeeded: ", response.data);
                            } else {
                                console.warn(this.tag+" operation failed: ", response.data.error);
                                this.$toasted.show("Send message API request failed, error: "+response.data.error, { duration: 3000 });
                            }
                        }.bind(this))

                        // If the request failed
                        .catch(function(error) {
                            console.warn(this.tag+" request failed", error);
                            this.$toasted.show("Send message API request failed, error: "+error, { duration: 3000 });
                        }.bind(this));

                }

            },
            getUsernameByPlayerId(id) {
                for (let i = 0; i < this.game.players.length; i++) {
                    if (this.game.players[i].id === id) {
                        return this.game.players[i].user.username;
                    }
                }
                return "Unknown";
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game-chat {
        height: 100%;
        display: flex;
        flex-direction: column;
        #game-chat__messages {
            flex: 1;
            padding: 30px;
            box-sizing: border-box;
            .game-chat__message {
                display: flex;
                margin: 0 0 15px 0;
                flex-direction: row;
                &:last-child {
                    margin: 0;
                }
                .message-author {
                    display: flex;
                    margin: 0 8px 0 0;
                    flex-direction: row;
                    align-items: center;
                    .message-author__pill {
                        font-size: .8em;
                        border-radius: 3px;
                        box-sizing: border-box;
                        background-color: #333;
                        padding: 2px 5px 3px 5px;
                    }
                }
                .message-text {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                }
            }
        }
        #game-chat__form {
            box-sizing: border-box;
            padding: 30px;
            #game-chat__input {
                width: 100%;
                padding: 5px 10px;
                border-radius: 3px;
                box-sizing: border-box;
                background-color: hsl(0, 0%, 45%);
            }
        }
    }
</style>