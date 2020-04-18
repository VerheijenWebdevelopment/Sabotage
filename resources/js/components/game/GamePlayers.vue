<template>
    <div id="game-players">

        <!-- Player -->
        <div class="game-player" v-for="(p, pi) in value" :key="pi" :class="{ you: p.id === player.id, active: p.id === playerAtPlay.id }">
            
            <!-- Online indicator -->
            <div class="game-player__online-indicator" :class="{ online: isOnline(p) }"></div>
            
            <!-- Avatar -->
            <div class="game-player__avatar" :style="{ backgroundImage: 'url('+p.user.avatar_url+')' }">
                <div class="avatar-prison-overlay" v-if="p.in_jail">
                    <div class="prison-bar"></div>
                    <div class="prison-bar"></div>
                    <div class="prison-bar"></div>
                    <div class="prison-bar"></div>
                    <div class="prison-bar"></div>
                </div>
            </div>
            
            <!-- Text -->
            <div class="game-player__text">
                <!-- Username -->
                <div class="game-player__username">{{ p.user.username }}</div>
                <!-- Flags -->
                <div class="game-player__flags">
                    <!-- Pickaxe -->
                    <div class="flag pickaxe" :class="{ available: p.pickaxe_available, unavailable: !p.pickaxe_available }">
                        <img class="flag-icon" :src="icons.pickaxe">
                    </div>
                    <!-- Light -->
                    <div class="flag light" :class="{ available: p.light_available, unavailable: !p.light_available }">
                        <img class="flag-icon" :src="icons.light">
                    </div>
                    <!-- Cart -->
                    <div class="flag cart" :class="{ available: p.cart_available, unavailable: !p.cart_available }">
                        <img class="flag-icon" :src="icons.cart">
                    </div>
                    <!-- In prison -->
                    <div class="flag prison" v-if="p.in_jail">
                        <img class="flag-icon" :src="icons.prison">
                    </div>
                    <!-- Thief -->
                    <div class="flag thiefery" v-if="p.thief_activated">
                        <img class="flag-icon" :src="icons.thief">
                    </div>
                </div>
            </div>

            <!-- Score -->
            <div class="game-player__score">
                <div class="score">
                    <div class="score-icon" :style="{ backgroundImage: 'url('+icons.gold+')' }"></div>
                    <div class="score-text">{{ p.score }}</div>
                </div>
            </div>

        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "icons",
            "player",
            "playerAtPlay",
            "value",
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
                console.log(this.tag+" icons: ", this.icons);
                console.log(this.tag+" value: ", this.value);
                this.startListening();
            },
            startListening() {
                console.log(this.tag+" starting to listen for events", this.game);
                // Join the game's chat presence channel
                Echo.join("game-chat."+this.game.id)
                    .here(this.onJoin)
                    .joining(this.onUserJoined)
                    .leaving(this.onUserLeft);
            },
            onJoin(players) {
                console.log("JOINED", players);
                // Load all online players when we succesfully join the channel
                for (let i = 0; i < players.length; i++) this.onlinePlayers.push(players[i].id);
            },
            onUserJoined(player) {
                // Add player to list of online players when they join the channel
                this.onlinePlayers.push(player.id);
            },
            onUserLeft(player) {
                // Remove the player from list of online players when they leave the channel
                for (let i = 0; i < this.onlinePlayers.length; i++) {
                    if (this.onlinePlayers[i] === player.id) {
                        this.onlinePlayers.splice(i, 1);
                        break;
                    }
                }
            },
            isOnline(player) {
                // Check if the given player is online or not
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
            overflow: hidden;
            border-radius: 3px;
            margin: 0 0 20px 0;
            position: relative;
            align-items: center;
            flex-direction: row;
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
                bottom: 6px;
                right: 6px;
                width: 6px;
                height: 6px;
                z-index: 5;
                position: absolute;
                border-radius: 3px;
                background-color: #a80000;
                &.online {
                    background-color: #1ac000;
                }
            }
            .game-player__avatar {
                height: 60px;
                flex: 0 0 60px;
                position: relative;
                background-color: #333;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center center;
                .avatar-prison-overlay {
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    flex-direction: row;
                    justify-content: space-evenly;
                    .prison-bar {
                        height: 100%;
                        flex: 0 0 4px;
                        background-color: #000;
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
                    font-size: .9em;
                    font-weight: 500;
                    line-height: 1em;
                    margin-bottom: 10px;
                }
                .game-player__score {
                    font-size: .8em;
                    color: rgba(255, 255, 255, 0.5);
                }
                .game-player__flags {
                    display: flex;
                    margin: 5px 0 0 0;
                    flex-direction: row;
                    .flag {
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
                            .flag-icon {
                                margin-bottom: 2px;
                            }   
                        }
                        &.cart {

                        }
                        &.available {
                            background-color: #0e6600;
                        }
                        &.unavailable, &.prison {
                            background-color: #a80000;
                        }
                        &.prison {
                            .flag-icon {
                                width: 16px;
                                margin-bottom: 2px;
                            }
                        }
                        &.thiefery {
                            background-color: #0081d1;
                        }
                        .flag-icon {
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
                margin: 0 25px;
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