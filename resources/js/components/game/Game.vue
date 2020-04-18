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
                        <game-board
                            :cards="cards"
                            v-model="mutableRound.board"
                            @clicked-tile="onClickBoardTile">
                        </game-board>
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
                                <div class="my-hand__card" v-for="(data, ci) in mutableHand" :key="ci" :class="{ selected: data.selected }">
                                    <div class="my-hand__card-image" :style="{ backgroundImage: 'url('+data.card.image_url+')' }" @click="onClickHandCard(ci)"></div>
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
                    
                    <!-- Card actions -->
                    <div id="card-actions__wrapper" :class="{ visible: showCardActions }">
                        <div id="card-actions" class="elevation-1">
                            <div class="card-action" v-if="numSelectedHandCards === 1">
                                <v-btn small dark @click="onClickPlayCard">
                                    <i class="fas fa-long-arrow-alt-up"></i>
                                    Speel kaart
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="numSelectedHandCards === 1">
                                <v-btn small dark @click="onClickFoldCard">
                                    <i class="far fa-times-circle"></i>
                                    Kaart afleggen
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="numSelectedHandCards > 1">
                                <v-btn small dark @click="onClickFoldCards">
                                    <i class="far fa-times-circle"></i>
                                    Kaarten afleggen
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="numSelectedHandCards === 2">
                                <v-btn small dark @click="onClickFoldCardsUnblock">
                                    <i class="far fa-times-circle"></i>
                                    Kaarten afleggen & deblokkeren
                                </v-btn>
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

        <!-- View (play) card dialog -->
        <v-dialog v-model="dialogs.play_card.show" width="600">
            <div class="dialog dark" v-if="dialogs.play_card.index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelPlayCard">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Kaart spelen</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="card mb-15 ma" :style="{ backgroundImage: 'url('+playCardDialogCard.image_url+')' }"></div>
                        Weet je zeker dat je deze kaart wilt afleggen?<br>
                        Nadat je de kaart aflegt trek je een nieuwe kaart en is je beurt voorbij.
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelPlayCard">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark color="red" @click="onClickConfirmPlayCard" :loading="dialogs.play_card.loading">
                            Speel kaart
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Fold card dialog -->
        <v-dialog v-model="dialogs.fold_card.show" width="600">
            <div class="dialog dark" v-if="dialogs.fold_card.index !== null && foldCardDialogCard !== undefined">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelFoldCard">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Kaart afleggen</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="card mb-15 ma" :style="{ backgroundImage: 'url('+foldCardDialogCard.image_url+')' }"></div>
                        Weet je zeker dat je deze kaart wilt afleggen?<br>
                        Nadat je de kaart aflegt trek je een nieuwe kaart en is je beurt voorbij.
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelFoldCard">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark color="red" @click="onClickConfirmFoldCard" :loading="dialogs.fold_card.loading">
                            <i class="fas fa-recycle"></i>
                            Kaart afleggen
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Fold cards dialog -->
        <v-dialog v-model="dialogs.fold_cards.show" width="700">
            <div class="dialog dark" v-if="dialogs.fold_cards.indices.length > 0 && foldCardsDialogCards.length > 0">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelFoldCards">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Kaarten afleggen</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="cards">
                            <div class="card mb-15" v-for="(card, ci) in foldCardsDialogCards" :key="ci" :style="{ backgroundImage: 'url('+card.image_url+')' }"></div>
                        </div>
                        Weet je zeker dat je deze kaarten wilt afleggen?<br>
                        Nadat je de kaarten aflegt trek je dezelfde hoeveelheid nieuwe kaarten en is je beurt voorbij.
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelFoldCards">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark color="red" @click="onClickConfirmFoldCards" :loading="dialogs.fold_cards.loading">
                            <i class="fas fa-recycle"></i>
                            Kaarten afleggen
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Fold cards and unblock tool dialog -->
        <v-dialog v-model="dialogs.fold_cards_unblock.show" width="600">
            
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
                play_card: {
                    show: false,
                    index: null,
                    loading: false,
                },
                fold_card: {
                    show: false,
                    index: null,
                    loading: false,
                },
                fold_cards: {
                    show: false,
                    indices: [],
                    loading: false,
                },
                fold_cards_unblock: {
                    show: false,
                    indices: [],
                    tool: null,
                    loading: false,
                },
            },
        }),
        computed: {
            // Turns
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
            // Hand
            selectedHandCards() {
                let out = [];
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        out.push(this.mutableHand[i]);
                    }
                }
                return out;
            },
            selectedHandCard() {
                let cards = this.selectedHandCards;
                if (cards.length > 0) {
                    return cards[0];
                }
                return false;
            },
            numSelectedHandCards() {
                return this.selectedHandCards.length;
            },
            showCardActions() {
                return this.numSelectedHandCards > 0 && this.numSelectedHandCards < 4;
            },
            // Play card dialog
            playCardDialogCard() {
                if (this.dialogs.play_card.index !== null) {
                    return this.mutableHand[this.dialogs.play_card.index].card;
                }
                return false;
            },
            // Fold card dialog
            foldCardDialogCard() {
                if (this.dialogs.fold_card.index !== null) {
                    return this.mutableHand[this.dialogs.fold_card.index].card;
                }
                return false;
            },
            // Fold cards dialog
            foldCardsDialogCards() {
                let out = [];
                for (let i = 0; i < this.dialogs.fold_cards.indices.length; i++) {
                    out.push(this.mutableHand[this.dialogs.fold_cards.indices[i]].card);
                }
                return out;
            },
            // Fold cards & unblock dialog

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
                this.initializeHand(this.hand);
            },
            initializeHand(hand) {
                this.mutableHand = [];
                if (hand !== undefined && hand !== null && hand.length > 0) {
                    for (let i = 0; i < hand.length; i++) {
                        let card = this.getCardById(hand[i]);
                        if (card) {
                            this.mutableHand.push({
                                selected: false,
                                card: card,
                            });
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
                this.initializeHand(e.hand);
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
            onClickHandCard(index) {
                console.log(this.tag+" clicked hand card: ", index);
                this.mutableHand[index].selected = ! this.mutableHand[index].selected;
            },
            onClickBoardTile(e) {
                console.log(this.tag+" clicked board tile: ", e);
            },
            // Hand actions
            onClickPlayCard() {
                console.log(this.tag+" clicked play card button");
                // Find & save selected card's index
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.dialogs.play_card.index = i;
                        break;
                    }
                }
                // Show dialog
                this.dialogs.play_card.show = true;
                // Deselect cards in our hand
                this.deselectHandCards();
            },
            onClickFoldCard() {
                console.log(this.tag+" clicked fold card button");
                // Find & save selected card's index
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.dialogs.fold_card.index = i;
                        break;
                    }
                }
                // Show dialog
                this.dialogs.fold_card.show = true;
                // Deselect cards in our hand
                this.deselectHandCards();
            },
            onClickFoldCards() {
                console.log(this.tag+" clicked fold cards button");
                // Find & save selected cards' indices
                this.dialogs.fold_cards.indices = [];
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.dialogs.fold_cards.indices.push(i);
                    }
                }
                // Show dialog
                this.dialogs.fold_cards.show = true;
                // Deselect cards in our hand
                this.deselectHandCards();
            },
            onClickFoldCardsUnblock() {
                console.log(this.tag+" clicked fold cards & unblock tool button");
                // Find & save selected cards' indices
                this.dialogs.fold_cards_unblock.indices = [];
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.dialogs.fold_cards_unblock.indices.push(i);
                    }
                }
                // Show dialog
                this.dialogs.fold_cards_unblock.show = true;
                // Deselect cards in our hand
                this.deselectHandCards();
            },
            deselectHandCards() {
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.mutableHand[i].selected = false;
                    }
                }
            },
            // Play card dialog
            onClickCancelPlayCard() {
                console.log(this.tag+" clicked fold card");
                this.dialogs.play_card.show = false;
            },
            onClickConfirmPlayCard() {
                console.log(this.tag+" clicked confirm play card");
                this.dialogs.play_card.loading = true;

            },
            // Fold card dialog
            onClickCancelFoldCard() {
                console.log(this.tag+" clicked cancel fold card button");
                this.dialogs.fold_card.show = false;
            },
            onClickConfirmFoldCard() {
                console.log(this.tag+" clicked confirm fold card button");
                // Start loading
                this.dialogs.fold_card.loading = true;
                // Send API request
                this.sendPerformActionRequest("fold_card", { index: this.dialogs.fold_card.index })
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Remove the played card from our hand
                        this.mutableHand.splice(this.dialogs.fold_card.index, 1);
                        // Add new card to hand if we received one
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false
                        });
                        // Stop loading
                        this.dialogs.fold_card.loading = false;
                        // Hide dialog
                        this.dialogs.fold_card.show = false;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.warn(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.fold_card.loading = false;
                    }.bind(this));
            },
            // Fold cards dialog 
            onClickCancelFoldCards() {
                console.log(this.tag+" clicked cancel fold cards button");
                this.dialogs.fold_cards.show = false;
            },
            onClickConfirmFoldCards() {
                console.log(this.tag+" clicked confirm fold cards button");
                // Start loading
                this.dialogs.fold_cards.loading = true;
                // Send API request
                this.sendPerformActionRequest("fold_cards", { indices: this.dialogs.fold_cards.indices })
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Remove cards from our hand
                        console.log("removing cards from hand", this.dialogs.fold_cards.indices);
                        let newHand = [];
                        for (let i = 0; i < this.mutableHand.length; i++) {
                            if (!this.dialogs.fold_cards.indices.includes(i)) {
                                newHand.push(this.mutableHand[i]);
                            }
                        }
                        this.mutableHand = newHand;
                        // Add new cards to our hand if we received any
                        console.log("adding cards to hand", response.data.new_cards);
                        if (response.data.new_cards.length > 0) {
                            for (let i = 0; i < response.data.new_cards.length; i++) {
                                this.mutableHand.push({
                                    card: response.data.new_cards[i],
                                    selected: false,
                                });
                                console.log(this.mutableHand);
                            }
                        }
                        // Stop loading
                        this.dialogs.fold_cards.loading = false;
                        // Hide dialog
                        this.dialogs.fold_cards.show = false;
                    }.bind(this))
                    // Request failed
                    .catch(function(error) {
                        console.warn(this.tag+" request failed: ", error);
                        console.log("WTF");
                        // Stop loading
                        this.dialogs.fold_cards.loading = false;
                    }.bind(this));
            },
            // Fold cards & unblock tool dialog
            onClickCancelFoldCardsUnblock() {
                console.log(this.tag+" clicked cancel fold cards & unblock tool button");
                this.dialogs.fold_cards_unblock.show = false;
            },
            onClickConfirmFoldCardsUnblock() {
                console.log(this.tag+" clicked confirm fold cards & unblock tool button");
                // Start loading
                this.dialogs.fold_cards_unblock.loading = true;
                // Make API request
                this.sendPerformActionRequest("fold_cards_unblock", { indices: this.dialogs.fold_cards_unblock.indices })
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Remove cards from our hand
                        for (let i = 0; i < this.dialogs.fold_cards.indices.length; i++) {
                            this.mutableHand.splice(this.dialogs.fold_cards.indicices[i], 1);
                        }
                        // Add new card to hand if we received one
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false
                        });
                        // Stop loading
                        this.dialogs.fold_cards_unblock.loading = false;
                        // Hide dialog
                        this.dialogs.fold_cards.unblock.show = false;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.warn(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.fold_cards_unblock.loading = false;
                    }.bind(this));
            },
            // API interaction
            sendPerformActionRequest(action, data) {
                return new Promise(function(resolve, reject) {
                    // Compose the payload to send to the API
                    let payload = new FormData();
                    payload.append("game_id", this.game.id);
                    payload.append("action", action);
                    payload.append("data", JSON.stringify(data));
                    // Send API request
                    this.axios.post(this.apiEndpoints.perform_action, payload)
                        // Request succeeded
                        .then(function(response) {
                            // Operation succeeded
                            if (response.data.status === 'success') {
                                console.log(this.tag+" operation succeeded: ", response.data);
                                // Resolve promise
                                resolve(response.data);
                            }
                            // Operation failed
                            else {
                                console.warn(this.tag+" operation failed: ", response.data.error);
                                // Toast the error message
                                this.$toasted.show("API operation failed: "+response.data.error, { duration: 3000 });
                                // Reject promise
                                reject(response.data.error);
                            }
                        }.bind(this))
                        // Request failed
                        .catch(function(error) {
                            console.warn(this.tag+" request failed, error: ", error);
                            // Toast the error
                            this.$toasted.show("API request failed: "+error, { duration: 3000 });
                            // Reject promise
                            reject(error);
                        }.bind(this));
                }.bind(this));
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
            z-index: 100;
            display: flex;
            flex: 0 0 250px;
            position: relative;
            flex-direction: row;
            box-sizing: border-box;
            padding: 20px 30px 30px 30px;
            background-color: hsl(0, 0%, 2%);
            .action-bar__title {
                font-size: 1em;
                font-weight: 600;
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
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    background-color: rgba(255, 255, 255, 0.15);
                    #deck__num-cards {
                        font-size: 1.5em;
                        font-weight: 500;
                        transition: all .3s;
                    }
                    #deck__text {}
                }
            }
        }
        #card-actions__wrapper {
            left: 0;
            width: 100%;
            z-index: 10;
            bottom: 221px;
            display: flex;
            position: absolute;
            transition: all .3s;
            flex-direction: row;
            justify-content: center;
            &.visible {
                bottom: 279px;
            }
            #card-actions {
                display: flex;
                margin: 0 auto;
                padding: 15px 10px;
                flex-direction: row;
                box-sizing: border-box;
                border-top-left-radius: 3px;
                border-top-right-radius: 3px;
                background-color: hsl(0, 0%, 5%);
                .card-action {
                    margin: 0 7px;
                    .v-btn {
                        margin: 0;
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
    .card {
        width:130px;
        height: 200px;
        overflow: hidden;
        border-radius: 3px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        &.mb-15 {
            margin-bottom: 15px;
        }
        &.ma {
            margin-left: auto;
            margin-right: auto;
        }
    }
    .cards {
        display: flex;
        margin-bottom: 15px;
        flex-direction: row;
        justify-content: center;
        .card {
            margin: 0 15px;
        }
    }
</style>