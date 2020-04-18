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
                    Main game
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

    }
    #round-over-ui {

    }
    #game-over-ui {

    }
</style>