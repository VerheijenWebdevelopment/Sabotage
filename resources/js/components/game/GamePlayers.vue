<template>
    <div id="game-players">
        <!-- Player -->
        <div class="game-player" v-for="(p, pi) in value" :key="pi" :class="{ you: p.id === player.id }">
            <!-- Avatar -->
            <div class="game-player__avatar">
                <!-- Online indicator -->
                <div class="game-player__online-indicator" :class="{ online: isOnline(p) }"></div>
            </div>
            <!-- Text -->
            <div class="game-player__text">
                <!-- Player # -->
                <div class="game-player__number">Player {{ p.player_number }}</div>
                <!-- Username -->
                <div class="game-player__username">{{ p.user.username }}</div>
                <!-- Score -->
                <div class="game-player__score">{{ p.score }} goud verzameld</div>
                <!-- Tools -->
                <div class="game-player__blocked-tools">
                    <!-- Pickaxe -->
                    <div class="blocked-tool" v-if="!p.pickaxe_available">
                        Pickaxe blocked
                    </div>
                    <!-- Light -->
                    <div class="blocked-tool" v-if="!p.light_available">
                        Light blocked
                    </div>
                    <!-- Cart -->
                    <div class="blocked-tool" v-if="!p.cart_available">
                        Cart blocked
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "player",
            "value",
            "cartIconUrl",
            "lightIconUrl",
            "pickaxeIconUrl",
        ],
        data: () => ({
            tag: "[game-players]",
            onlinePlayers: [],
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" value: ", this.value);
                console.log(this.tag+" cart icon url: ", this.cartIconUrl);
                console.log(this.tag+" light icon url: ", this.lightIconUrl);
                console.log(this.tag+" pickaxe icon url: ", this.pickaxeIconUrl);
                this.startListening();
            },
            startListening() {
                // Join the Game's channel
                Echo.join("game-chat."+this.game.id)
                    // When we join the channel
                    .here(this.onJoin)
                    // When player joins the channel
                    .joining(this.onUserJoined)
                    // When player leaves the channel
                    .leaving(this.onUserLeft);
            },
            onJoin(players) {
                console.log(this.tag+" joined the game channel", players);
                for (let i = 0; i < players.length; i++) {
                    this.onlinePlayers.push(players[i].id);
                }
            },
            onUserJoined(player) {
                console.log(this.tag+" user joined the game channel", player);
                this.onlinePlayers.push(player.id);
            },
            onUserLeft(player) {
                console.log(this.tag+" user left the game channel", player);
                for (let i = 0; i < this.onlinePlayers.length; i++) {
                    if (this.onlinePlayers[i] === player.id) {
                        this.onlinePlayers.splice(i, 1);
                        break;
                    }
                }
            },
            isOnline(player) {
                for (let i = 0; i < this.onlinePlayers.length; i++) {
                    if (this.onlinePlayers[i] === player.id) {
                        return true;
                    }
                }
                return false;
            }
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game-players {
        display: flex;
        flex-direction: row;
        justify-content: center;
        .game-player {
            display: flex;
            flex: 0 0 250px;
            align-items: center;
            flex-direction: row;
            &.you {
                .game-player__avatar {
                    border: 2px solid #ffd900;
                }
            }
            .game-player__avatar {
                height: 40px;
                flex: 0 0 40px;
                position: relative;
                border-radius: 20px;
                border: 2px solid #000;
                background-color: #333;
                background-repeat: no-repeat;
                background-position: center center;
                .game-player__online-indicator {
                    width: 10px;
                    height: 10px;
                    right: -10px;
                    bottom: -10px;
                    position: absolute;
                    border-radius: 5px;
                    background-color: #a80000;
                    &.online {
                        background-color: #1ac000;
                    }
                }
            }
            .game-player__text {
                flex: 1;
                margin: 0 0 0 15px;
                .game-player__number {
                    font-size: .7em;
                    color: rgba(255, 255, 255, 0.25);
                }
                .game-player__username {
                    font-size: 1em;
                    font-weight: 500;
                }
                .game-player__score {
                    font-size: .8em;
                    color: rgba(255, 255, 255, 0.5);
                }
                .game-player__tools {
                    display: flex;
                    flex-direction: row;
                    .tool {
                        &.available {
                            .tool-image {
                                background-color: #1ac000;
                            }
                        }
                        &.blocked {
                            .tool-image {
                                background-color: #a80000;
                            }
                        }
                        .tool-image {
                            width: 50px;
                            height: 50px;
                            background-size: contain;
                            background-repeat: no-repeat;
                            background-position: center center;
                        }
                    }
                }
            }
        }   
    }
</style>