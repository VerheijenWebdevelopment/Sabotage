<template>
    <div id="game__wrapper">

        <!-- Game -->
        <div id="game" v-if="mutableGame !== null && mutableRound !== null">
            <!-- Content area -->
            <div id="game-content">

                <!-- Role selection phase -->
                <div id="role-selection-ui" v-if="mutableRound.phase === 'role_selection'">

                    <game-role-selection
                        :roles="roles"
                        :game="mutableGame"
                        :round="mutableRound"
                        :player="mutablePlayer"
                        :player-role="mutablePlayerRole"
                        :player-at-play="playerAtPlay"
                        :icons="icons"
                        :api-endpoints="apiEndpoints">
                    </game-role-selection>

                </div>

                <!-- Main game phase -->
                <div id="game-ui" v-if="mutableRound.phase === 'main'">
                    
                    <!-- Game board -->
                    <div id="game-ui__board">
                        board
                    </div>

                    <!-- Action bar -->
                    <div id="game-ui__action-bar">

                        <!-- My role -->
                        <div id="action-bar__my-role">
                            <h3 class="action-bar__title">Mijn rol</h3>
                            <div id="my-role-card" @click="onClickMyRoleCard">
                                <div id="my-role-card__title">{{ mutablePlayerRole.label }}</div>
                                <div id="my-role-card__subtitle" class="blue-text" v-if="mutablePlayerRole.name === 'blue_digger'">
                                    Blauwe team
                                </div>
                                <div id="my-role-card__subtitle" class="green-text" v-if="mutablePlayerRole.name === 'green_digger'">
                                    Groene team
                                </div>
                                <div id="my-role-card__icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                            </div>
                        </div>

                        <!-- My hand -->
                        <div id="action-bar__my-hand">
                            <h3 class="action-bar__title">Mijn hand</h3>
                            <!-- Cards -->
                            <div id="my-hand__cards" v-if="mutableHand.length > 0">
                                <div class="my-hand__card" v-for="(card, ci) in mutableHand" :key="ci">
                                    <div class="my-hand__card-image" :style="{ backgroundImage: 'url('+card.image_url+')' }" @click="onClickHandCard(ci)"></div>
                                </div>
                            </div>
                            <!-- No cards in your hand -->
                            <div id="my-hand__no-cards" v-if="mutableHand.length === 0">
                                Je hand is momenteel leeg
                            </div>
                        </div>

                        <!-- The deck -->
                        <div id="action-bar__deck">
                            <h3 class="action-bar__title">Het deck</h3>
                            <div id="deck">
                                <div id="deck__num-cards">{{ mutableRound.num_cards_in_deck }}</div>
                                <div id="deck__text" v-if="mutableRound.num_cards_in_deck === 1">Kaart</div>
                                <div id="deck__text" v-if="mutableRound.num_cards_in_deck > 1">Kaarten</div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- End of round (reward) phase -->
                <div id="round-over-ui" v-if="mutableRound.phase === 'rewards'">
                    End of round
                </div>

                <!-- End of game phase -->
                <div id="game-over-ui" v-if="mutableRound.phase === 'endgame'">
                    End of game
                </div>

            </div>
            <!-- Sidebar -->
            <div id="game-sidebar">
                <div id="game-sidebar__players">
                    <game-players
                        :game="game"
                        :icons="icons"
                        :player="player"
                        :player-at-play="playerAtPlay"
                        v-model="mutablePlayers">
                    </game-players>
                </div>
                <div id="game-sidebar__chat">
                    <game-chat
                        :game="game"
                        :player="player"
                        :send-message-api-endpoint="apiEndpoints.send_message">
                    </game-chat>
                </div>
            </div>
        </div>

        <!-- My role dialog -->
        <v-dialog v-model="dialogs.my_role.show" width="500">
            <div class="dialog dark" v-if="mutablePlayerRole">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.my_role.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <h3 class="dialog-title">Mijn rol</h3>
                    <!-- Text -->
                    <div class="dialog-text nm">
                        <div id="my-role-dialog">
                            <div id="my-role-dialog__card"></div>
                            <div id="my-role-dialog__text">
                                <div id="my-role-dialog__name">
                                    {{ mutablePlayerRole.label }}
                                    <span class="blue-text" v-if="mutablePlayerRole.name === 'blue_digger'">Blauwe team</span>    
                                    <span class="blue-text" v-if="mutablePlayerRole.name === 'green_digger'">Groene team</span>    
                                </div>
                                <div id="my-role-dialog__description">{{ mutablePlayerRole.description }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-dialog>

    </div>
</template>

<script>
    import { EventBus } from './../../event-bus.js';
    export default {
        props: [
            "game",
            "round",
            "player",
            "playerRole",
            "hand",
            "roles",
            "cards",
            "icons",
            "apiEndpoints",
        ],
        data: () => ({
            tag: "[game]",
            mutableGame: [],
            mutableRound: [],
            mutablePlayer: null,
            mutablePlayerRole: null,
            mutablePlayers: [],
            mutableHand: [],
            dialogs: {
                my_role: {
                    show: false,
                },
            },
        }),
        computed: {
            playerAtPlay() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].player_number == this.mutableRound.players_turn) {
                        return this.mutablePlayers[i];
                    }
                }
                return false;
            },
            itsMyTurn() {
                return this.playerAtPlay.id === this.player.id;
            },
        },
        methods: {
            initialize() {
                // console.log(this.tag+" initializing");
                // console.log(this.tag+" game: ", this.game);
                // console.log(this.tag+" round: ", this.round);
                // console.log(this.tag+" player: ", this.player);
                // console.log(this.tag+" player role: ", this.playerRole);
                // console.log(this.tag+" hand: ", this.hand);
                // console.log(this.tag+" roles: ", this.roles);
                // console.log(this.tag+" cards: ", this.cards);
                // console.log(this.tag+" icons: ", this.icons);
                // console.log(this.tag+" api endpoints: ", this.apiEndpoints);
                this.initializeData();
                this.startListening();
            },
            initializeData() {
                // Make all the received data mutable
                this.mutableGame = this.game;
                this.mutableRound = this.round;
                this.mutablePlayer = this.player;
                if (this.playerRole) this.mutablePlayerRole = this.playerRole;
                if (this.game !== undefined && this.game.players !== undefined && this.game.players.length > 0) {
                    for (let i = 0; i < this.game.players.length; i++) {
                        this.mutablePlayers.push(this.game.players[i]);
                    }
                }
                if (this.hand !== undefined && this.hand !== null && this.hand.length > 0) {
                    for (let i = 0; i < this.hand.length; i++) {
                        let card = this.getCardById(this.hand[i]);
                        if (card) {
                            this.mutableHand.push(card);
                        }
                    }
                }
            },
            startListening() {

                // Role selection component events
                EventBus.$on("role_selected", this.onRoleSelected);

                // Game channel events
                Echo.private('game.'+this.game.id)
                    .listen('Game\\PlayerSelectedRole', this.onPlayerSelectedRole)
                    .listen('Game\\PlayerToolBlocked', this.onPlayerToolBlocked)
                    .listen('Game\\PlayerToolRecovered', this.onPlayerToolRecovered)
                    .listen('Game\\PlayerCheckedGoldLocation', this.onPlayerCheckedGoldLocation)
                    .listen('Game\\GoldLocationRevealed', this.onGoldLocationRevealed)
                    .listen('Game\\PlayerPlacedTunnel', this.onPlayerPlacedTunnel)
                    .listen('Game\\PlayerCollapsedTunnel', this.onPlayerCollapsedTunnel)
                    .listen('Game\\PlayerWasAwardedGold', this.onPlayerWasAwardedGold)
                    .listen('Game\\TurnEnded', this.onTurnEnded)
                    .listen('Game\\PlayerIsReadyForNextRound', this.onPlayerReadyForNextRound)
                    .listen('Game\\NewRoundStarted', this.onNewRoundStarted)
                    .listen('Game\\RoundEnded', this.onRoundEnded)
                    .listen('Game\\GameEnded', this.onGameEnded);

            },
            // Event handlers
            onRoleSelected(e) {
                console.log(this.tag+" role selected event received: ", e);
                
                // Update the player's hand & role
                this.mutableHand = e.hand;
                this.mutablePlayerRole = e.role;

                // Update the round
                this.mutableRound.players_with_selected_roles.push(this.mutablePlayer.id);
                this.mutableRound.num_cards_in_role_deck -= 1;

                // Switch to the main phase when all players have selected their role
                if (this.mutableRound.players_with_selected_roles.length === this.mutablePlayers.length) {
                    setTimeout(function() {
                        this.mutableRound.phase = "main";
                    }.bind(this), 2000);
                }

            },
            onPlayerSelectedRole(e) {
                console.log(this.tag+"[event] received event player selected role:", e);

                // Add player's ID to the list of players who have selected a role
                this.mutableRound.players_with_selected_roles.push(e.player.id);
                this.mutableRound.num_cards_in_role_deck -= 1;

                // Switch to the main phase when all players have selected their roles
                if (this.mutableRound.players_with_selected_roles.length === this.mutablePlayers.length) {
                    setTimeout(function() {
                        this.mutableRound.phase = "main";
                    }.bind(this), 2000);
                }

            },
            onPlayerToolBlocked(e) {
                console.log(this.tag+"[event] received event player tool blocked:", e);

            },
            onPlayerToolRecovered(e) {
                console.log(this.tag+"[event] received event player tool recovered:", e);

            },
            onPlayerCheckedGoldLocation(e) {
                console.log(this.tag+"[event] received event player checked gold location:", e);

            },
            onGoldLocationRevealed(e) {
                console.log(this.tag+"[event] received event gold location revealed:", e);

            },
            onPlayerPlacedTunnel(e) {
                console.log(this.tag+"[event] received event player placed tunnel:", e);

            },
            onPlayerCollapsedTunnel(e) {
                console.log(this.tag+"[event] received event player collapsed tunnel:", e);

            },
            onPlayerWasAwardedGold(e) {
                console.log(this.tag+"[event] received event player was awarded gold:", e);

            },
            onTurnEnded(e) {
                console.log(this.tag+"[event] received event turn ended:", e);
                
                this.mutableRound.players_turn = e.game.current_round.players_turn;
                this.mutableRound.num_cards_in_deck = e.game.current_round.num_cards_in_deck;

            },
            onRoundEnded(e) {
                console.log(this.tag+"[event] received event round ended:", e);

            },
            onPlayerReadyForNextRound(e) {
                console.log(this.tag+"[event] received event player ready for next round:", e);

            },
            onNewRoundStarted(e) {
                console.log(this.tag+"[event] received event new round started:", e);

            },
            onGameEnded(e) {
                console.log(this.tag+"[event] received event game ended:", e);

            },
            // UI event handlers
            onClickMyRoleCard() {
                console.log(this.tag+" clicked my role card");
                this.dialogs.my_role.show = true;
            },
            // Getters
            getCardById(id) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].id === id) {
                        return this.cards[i];
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
    #game__wrapper {
        width: 100%;
        height: 100%;
        #game {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            #game-content {
                flex: 1;
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            #game-sidebar {
                display: flex;
                flex: 0 0 400px;
                flex-direction: column;
                background-color: hsl(0, 0%, 5%);
                #game-sidebar__players {
                    flex: 1;
                }
                #game-sidebar__chat {
                    flex: 0 0 300px;
                }
            }
        }
    }
    #role-selection-ui {
        width: 100%;
        height: 100%;
    }
    #game-ui {
        width: 100%;
        height: 100%;
        display: flex;
        position: relative;
        flex-direction: column;
        #game-ui__board {
            flex: 1;
        }
        #game-ui__action-bar {
            display: flex;
            padding: 30px;
            flex: 0 0 250px;
            flex-direction: row;
            box-sizing: border-box;
            background-color: hsl(0, 0%, 2%);
            .action-bar__title {
                font-size: 1.1em;
                font-weight: 500;
            }
            #action-bar__my-role {
                flex: 0 0 130px;
                margin: 0 30px 0 0;
                #my-role-card {
                    width: 130px;
                    height: 200px;
                    display: flex;
                    color: #fff;
                    border-radius: 3px;
                    position: relative;
                    transition: all .3s;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    background-color: rgba(255, 255, 255, 0.10);
                    &:hover {
                        cursor: pointer;
                        background-color: rgba(255, 255, 255, 0.5);
                        #my-role-card__icon {
                            opacity: 0.5;
                        }
                        #my-role-card__title {
                            margin-top: -75px;
                        }
                    }
                    #my-role-card__title {
                        font-weight: 500;
                        transition: all .3s;
                    }
                    #my-role-card__subtitle {

                    }
                    #my-role-card__icon {
                        left: 0;
                        opacity: 0;
                        width: 100%;
                        bottom: 15px;
                        font-size: 2em;
                        position: absolute;
                        text-align: center;
                        transition: all .3s;
                    }
                }
            }
            #action-bar__my-hand {
                flex: 1;
                display: flex;
                margin: 0 30px 0 0;
                align-items: center;
                flex-direction: column;
                justify-content: center;
                .action-bar__title {
                    text-align: center;
                }
                #my-hand__cards {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    .my-hand__card {
                        margin: 0 15px 0 0;
                        &:hover {
                            cursor: pointer;
                        }
                        &:last-child {
                            margin: 0;
                        }
                        &.selected {
                            .my-hand__card-image {
                                border: 2px solid #ffd900;
                            }
                        }
                        .my-hand__card-image {
                            width: 130px;
                            height: 200px;
                            border-radius: 3px;
                            background-size: contain;
                            background-repeat: no-repeat;
                            background-position: center center;
                            background-color: hsl(0, 0%, 20%);
                        }
                    }
                }
                #my-hand__no-cards {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                }
            }
            #action-bar__deck {
                .action-bar__title {
                    text-align: right;
                }
                #deck {
                    width: 130px;
                    height: 200px;
                    display: flex;
                    color: #fff;
                    border-radius: 3px;
                    position: relative;
                    transition: all .3s;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    background-color: rgba(255, 255, 255, 0.15);
                    #deck__num-cards {
                        font-weight: 500;
                        transition: all .3s;
                    }
                    #deck__text {
                    }
                }
            }
        }
    }
    #round-over-ui {
        width: 100%;
        height: 100%;
    }
    #game-over-ui {
        width: 100%;
        height: 100%;
    }
    #my-role-dialog {
        display: flex;
        flex-direction: row;
        #my-role-dialog__card {
            height: 200px;
            flex: 0 0 130px;
            border-radius: 3px;
            background-color: hsl(0, 0%, 90%);
        }
        #my-role-dialog__text {
            flex: 1;
            margin: 0 0 0 30px;
            #my-role-dialog__name {
                font-size: 1.2em;
                margin: 0 0 5px 0;
            }
            #my-role-dialog__description {

            }
        }
    }
</style>