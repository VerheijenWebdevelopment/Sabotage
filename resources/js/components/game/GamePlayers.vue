<template>
    <div id="game-players">
        <!-- Player -->
        <div class="game-player" v-for="(p, pi) in value" :key="pi" :class="{ you: p.id === player.id, active: p.id === playerAtPlay.id }">
            <!-- Online indicator -->
            <div class="game-player__online-indicator" :class="{ online: isOnline(p) }"></div>
            <!-- Avatar -->
            <div class="game-player__avatar">
            </div>
            <!-- Text -->
            <div class="game-player__text">
                <!-- Player # -->
                <!-- <div class="game-player__number">Player {{ p.player_number }}</div> -->
                <!-- Username -->
                <div class="game-player__username">{{ p.user.username }}</div>
                <!-- Tools -->
                <div class="game-player__tools">
                    <!-- Pickaxe -->
                    <div class="tool pickaxe" :class="{ available: p.pickaxe_available, unavailable: !p.pickaxe_available }">
                        <img class="tool-icon" :src="pickaxeIconUrl">
                    </div>
                    <!-- Light -->
                    <div class="tool light" :class="{ available: p.light_available, unavailable: !p.light_available }">
                        <img class="tool-icon" :src="lightIconUrl">
                    </div>
                    <!-- Cart -->
                    <div class="tool cart" :class="{ available: p.cart_available, unavailable: !p.cart_available }">
                        <img class="tool-icon" :src="cartIconUrl">
                    </div>
                </div>
            </div>
            <!-- Score -->
            <div class="game-player__score">
                <div class="score">
                    <div class="score-icon" :style="{ backgroundImage: 'url('+goldIconUrl+')' }"></div>
                    <div class="score-text">
                        {{ p.score }}
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
            "playerAtPlay",
            "value",
            "cartIconUrl",
            "lightIconUrl",
            "pickaxeIconUrl",
            "goldIconUrl",
        ],
        data: () => ({
            tag: "[game-players]",
            onlinePlayers: [],
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" player: ", this.player);
                console.log(this.tag+" player at play: ", this.playerAtPlay);
                console.log(this.tag+" value: ", this.value);
                console.log(this.tag+" cart icon url: ", this.cartIconUrl);
                console.log(this.tag+" light icon url: ", this.lightIconUrl);
                console.log(this.tag+" pickaxe icon url: ", this.pickaxeIconUrl);
                console.log(this.tag+" gold icon url: ", this.goldIconUrl);
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
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game-players {
        display: flex;
        padding: 30px;
        box-sizing: border-box;
        flex-direction: column;
        justify-content: center;
        .game-player {
            display: flex;
            padding: 10px;
            border-radius: 3px;
            margin: 0 0 15px 0;
            position: relative;
            align-items: center;
            flex-direction: row;
            box-sizing: border-box;
            background-color: hsl(0, 0%, 2%);
            &:last-child {
                margin: 0;
            }
            &.you {
                .game-player__avatar {
                    // border: 2px solid #ffd900;
                }
            }
            &.active {
                border-bottom: 2px solid #ffd900;
            }
            .game-player__online-indicator {
                left: 6px;
                width: 6px;
                bottom: 6px;
                height: 6px;
                position: absolute;
                border-radius: 3px;
                background-color: #a80000;
                &.online {
                    background-color: #1ac000;
                }
            }
            .game-player__avatar {
                height: 40px;
                flex: 0 0 40px;
                position: relative;
                border-radius: 20px;
                // border: 2px solid #000;
                background-color: #333;
                background-repeat: no-repeat;
                background-position: center center;
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
                    margin: 5px 0 0 0;
                    flex-direction: row;
                    .tool {
                        height: 20px;
                        display: flex;
                        flex: 0 0 20px;
                        border-radius: 2px;
                        align-items: center;
                        box-sizing: border-box;
                        justify-content: center;
                        margin: 0 10px 0 0;
                        background-color: hsl(0, 0%, 5%);
                        &:last-child {
                            margin: 0;
                        }
                        &.pickaxe {

                        }
                        &.light {
                            .tool-icon {
                                width: 16px;
                                height: 16px;
                                margin: 4px 2px 0 0;
                            }
                        }
                        &.cart {

                        }
                        &.available {
                            background-color: #0e6600;
                        }
                        &.unavailable {
                            background-color: #a80000;
                        }
                        .tool-icon {
                            width: 20px;
                            height: 20px;
                            margin: 5px 0px 0 0;
                            background-size: contain;
                            background-repeat: no-repeat;
                            background-position: center center;
                        }
                    }
                }
            }
            .game-player__score {
                display: flex;
                margin: 0 0 0 15px;
                align-items: center;
                justify-content: center;
                .score {
                    display: flex;
                        align-items: center;
                    // align-items: center;
                    flex-direction: row;
                    .score-icon {
                        width: 40px;
                        height: 40px;
                        margin: 7px 0 0 0;
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center center;
                    }
                    .score-text {
                        display: flex;
                        flex-direction: row;
                    }
                }
            }
        }   
    }
</style>