<template>
    <div id="game">

        <!-- Content -->
        <div id="game-content">

            <!-- Role selection UI -->
            <div id="role-selection" v-if="mutableGame !== null && phase === 'role_selection'">

                <!-- Role assigned -->
                <div id="role-assigned" v-if="mutablePlayerRole !== null">

                    <!-- Title -->
                    <h1>Role assigned</h1>

                    <!-- Role card -->
                    <div class="role-card no-hover">
                        <div class="role-card__label">{{ mutablePlayerRole.label }}</div>
                    </div>

                    <!-- Please wait text -->
                    <div id="role-selection__text" v-if="playerAtPlay">
                        {{ playerAtPlay.user.username }} is currently picking a role.
                    </div>

                </div>

                <!-- Role not assigned yet -->
                <div id="role-not-assigned" v-if="mutablePlayerRole === null">

                    <!-- Title -->
                    <h1>Role selection</h1>

                    <!-- Please wait on your turn text -->
                    <div id="role-selection__text" v-if="!myTurn && playerAtPlay">
                        {{ playerAtPlay.user.username }} is currently picking a role.    
                    </div>

                    <!-- Available roles -->
                    <div id="available-roles">
                        <!-- Title -->
                        <h3>Available roles</h3>
                        <!-- Roles -->
                        <div id="available-roles__list" v-if="mutableGame.available_roles.length > 0">
                            <div class="available-role__wrapper" v-for="(role, ri) in mutableGame.available_roles" :key="ri">
                                <div class="available-role">
                                    <div class="role-name">{{ getRoleLabelById(role.role_id) }}</div>
                                    <div class="role-amount">x{{ role.count }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- No roles -->
                        <div id="no-roles-available" v-if="mutableGame.available_roles.length === 0">
                            No roles available, this shouldn't happen
                        </div>
                    </div>

                    <!-- Select card -->
                    <div id="role-cards" v-if="myTurn && !selectedRoleCard">
                        <!-- Title -->
                        <h2>Role cards</h2>
                        <h3>Please choose one of the available role cards</h3>
                        <!-- Cards -->
                        <div id="role-cards__list" v-if="!selectedRoleCard">
                            <div class="role-card__wrapper" v-for="(n, ni) in mutableGame.num_available_roles" :key="ni">
                                <div class="role-card" @click="onClickRoleCard(ni)">
                                    <div class="role-card__title">Role card</div>
                                    <div class="role-card__number">#{{ n }}</div>
                                    <div class="role-card__select">
                                        Select card
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card selected & loading -->
                    <div id="role-card__selected" v-if="selectedRoleCard && selectRole.loading">
                        <!-- Title -->
                        <h2>Role card selected</h2>
                        <!-- Content -->
                        <div id="role-card__selected-content">
                            <!-- Selected card -->
                            <!-- <div id="role-card__selected-card">
                                <div id="selected-card__title">Role card</div>
                                <div id="selected-card__number"># {{ selectedRoleCard }}</div>
                            </div> -->
                            <!-- Text -->
                            <div id="role-card__selected-text">
                                <div id="selected-text__loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div id="selected-text__title">Loading</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Game UI -->
            <div id="game-ui" v-if="mutableGame !== null && phase === 'main'">

                <!-- Game board -->
                <div id="board-area">

                    <!-- Game info -->
                    <div id="game-info">
                        <!-- Current round -->
                        <div id="game-info__current-round">Round {{ round }}</div>
                        <!-- Current player at play -->
                        <div id="game-info__player-turn" v-if="playerAtPlay">
                            <div id="my-turn" v-if="myTurn">
                                It's your turn!
                            </div>
                            <div id="not-my-turn" v-if="!myTurn">
                                It's <span>{{ playerAtPlay.user.username }}</span>'s turn
                            </div>
                        </div>
                    </div>

                    <!-- Game board -->
                    <div id="game-board__wrapper">
                        <game-board
                            v-model="mutableBoard"
                            :cards="cards"
                            @clicked-tile="onClickedBoardTile">
                        </game-board>
                    </div>

                </div>

                <!-- Action area -->
                <div id="action-area">

                    <!-- My role -->
                    <div id="my-role">
                        <!-- Title -->
                        <div id="my-role__title">My role</div>
                        <!-- Role card -->
                        <div id="my-role__card">
                            <div id="my-role__card-text">
                                {{ mutablePlayerRole.label }}
                            </div>
                        </div>
                    </div>

                    <!-- My cards -->
                    <div id="my-hand">
                        <!-- Title -->
                        <div id="my-hand__title">My hand</div>
                        <!-- Cards -->
                        <div id="my-hand__cards" v-if="mutableHand.length > 0">
                            <div class="my-hand__card" v-for="(card, ci) in mutableHand" :key="ci" :class="{ selected: card.selected }">
                                <div class="my-hand__card-image" :style="{ backgroundImage: 'url('+card.data.image_url+')' }" @click="onClickHandCard(ci)"></div>
                            </div>
                        </div>
                        <!-- No cards in your hand -->
                        <div id="my-hand__no-cards" v-if="mutableHand.length === 0">
                            No cards in your hand at the moment
                        </div>
                    </div>

                    <!-- My actions -->
                    <div id="my-actions">
                        <!-- Title -->
                        <div id="my-actions__title">Actions</div>
                        <!-- Actions -->
                        <div id="my-actions__list" v-if="myTurn && selectedHandCards.length === 1">
                            <!-- Play card -->
                            <div class="action">
                                <v-btn block @click="onClickPlayCard" :loading="playCard.loading">
                                    Play card
                                </v-btn>
                            </div>
                            <!-- Fold card -->
                            <div class="action">
                                <v-btn block @click="onClickFoldCard" :loading="foldCard.loading">
                                    Fold card
                                </v-btn>
                            </div>
                        </div>
                        <!-- Too many cards selected -->
                        <div id="my-actions__too-many-cards" v-if="myTurn && selectedHandCards.length > 1">
                            Too many cards selected
                        </div>
                        <!-- No card selected -->
                        <div id="my-actions__select-card" v-if="myTurn && selectedHandCards.length === 0">
                            Select a card in your hand to perform an action
                        </div>
                        <!-- Wait on your turn -->
                        <div id="my-actions__wait" v-if="!myTurn">
                            You can't perform an action until it's your turn.
                        </div>
                    </div>

                </div>

            </div>

            <!-- Rewards UI -->
            <div id="rewards-ui" v-if="mutableGame !== null && phase === 'rewards'">
                Rewards
            </div>

        </div>

        <!-- Sidebar -->
        <div id="game-sidebar">

            <!-- Players -->
            <div id="game-players__wrapper">
                <game-players
                    :game="game"
                    :player="player"
                    :player-at-play="playerAtPlay"
                    v-model="mutablePlayers"
                    :cart-icon-url="cartIconUrl"
                    :light-icon-url="lightIconUrl"
                    :pickaxe-icon-url="pickaxeIconUrl"
                    :gold-icon-url="goldIconUrl">
                </game-players>
            </div>

            <!-- Chat -->
            <div id="game-chat__wrapper">
                <game-chat
                    :game="game"
                    :player="player"
                    :send-message-api-endpoint="sendMessageApiEndpoint">
                </game-chat>
            </div>

        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "player",
            "playerRole",
            "hand",
            "roles",
            "cards",
            "sendMessageApiEndpoint",
            "performActionApiEndpoint",
            "cartIconUrl",
            "lightIconUrl",
            "pickaxeIconUrl",
            "goldIconUrl",
        ],
        data: () => ({
            tag: "[game]",
            turn: 1,
            round: 1,
            phase: null,
            playersTurn: 1,
            mutablePlayers: [],
            mutableGame: null,
            mutablePlayer: null,
            mutablePlayerRole: null,
            mutableHand: [],
            mutableBoard: null,
            selectRole: {
                loading: false,
                selectedCardIndex: null,
            },
            foldCard: {
                loading: false,
            },
            playCard: {
                loading: false,
                dialogs: {
                    selectPlayer: {
                        show: false,
                        selectedPlayer: null,
                    },
                    selectGoldLocation: {
                        show: false,
                        selectedLocation: null,
                    },
                    placeTunnel: {
                        show: false,
                        selectedPosition: null,
                    }
                }
            },
        }), 
        computed: {
            playerAtPlay() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].player_number == this.playersTurn) return this.mutablePlayers[i];
                }
                return false;
            },
            myTurn() {
                return this.playerAtPlay.id === this.player.id;
            },
            hasSelectedRole() {
                return this.mutablePlayerRole !== null;
            },
            numRoleCardOptions() {
                return this.mutableGame.num_available_roles - this.mutableGame.players_with_selected_roles;
            },
            selectedRoleCard() {
                if (this.selectRole.selectedCardIndex !== null) {
                    return this.selectRole.selectedCardIndex + 1;
                }
                return false;
            },
            allPlayersHaveRoles() {
                if (this.mutableGame !== null && this.mutablePlayers !== null) {
                    return this.mutableGame.players_with_selected_roles.length === this.mutablePlayers.length;
                }
                return false;
            },
            selectedHandCards() {
                let out = [];
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        out.push(this.mutableHand[i]);
                    }
                }
                return out;
            },
            selectedHandCardIndex() {
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        return i;
                    }
                }
                return false;
            },
        },
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" player: ", this.player);
                console.log(this.tag+" player role: ", this.playerRole);
                console.log(this.tag+" hand: ", this.hand);
                console.log(this.tag+" roles: ", this.roles);
                console.log(this.tag+" cards: ", this.cards);
                console.log(this.tag+" send message api endpoint: ", this.sendMessageApiEndpoint);
                console.log(this.tag+" perform action api endpoint: ", this.performActionApiEndpoint);
                console.log(this.tag+" cart icon url: ", this.cartIconUrl);
                console.log(this.tag+" light icon url: ", this.lightIconUrl);
                console.log(this.tag+" pickaxe icon url: ", this.pickaxeIconUrl);
                console.log(this.tag+" gold icon url: ", this.goldIconUrl);
                // console.log(this.tag+" ");
                this.initializeData();
                this.startListening();
            },
            initializeData() {

                // Make the game mutable
                this.mutableGame = this.game;

                // Make current player mutable
                this.mutablePlayer = this.player;

                // Make current player's role mutable (if it has been selected, otherwise leave it at null)
                if (this.playerRole) this.mutablePlayerRole = this.playerRole;

                // Make game's players mutable
                if (this.game !== undefined && this.game !== null) {
                    for (let i = 0; i < this.game.players.length; i++) {
                        this.mutablePlayers.push(this.game.players[i]);
                    }
                }

                // Make the player's hand mutable
                if (this.hand !== undefined && this.hand !== null && this.hand && this.hand.length > 0) {
                    this.initializeHand(this.hand);
                }

                // Make the board mutable
                this.mutableBoard = this.game.board;

                // Initialize the game's state
                this.turn = this.game.turn;
                this.round = this.game.round;
                this.phase = this.game.phase;
                this.playersTurn = this.game.player_turn;

            },
            initializeHand(hand) {
                this.mutableHand = [];
                for (let i = 0; i < hand.length; i++) {
                    let card = this.getCardById(hand[i]);
                    if (card) {
                        let cardEntry = {
                            data: card,
                            selected: false,
                        }
                        this.mutableHand.push(cardEntry);
                    }
                }
            },
            startListening() {
                // Connect to the game's channel
                Echo.private('game.'+this.game.id)
                    .listen('Game\\PlayerSelectedRole', this.onPlayerSelectedRole)
                    .listen('Game\\TurnEnded', this.onTurnEnded)
                    .listen('Game\\RoundEnded', this.onRoundEnded)
                    .listen('Game\\GameEnded', this.onGameEnded)
            },
            onPlayerSelectedRole(e) {
                console.log(this.tag+"[event] player selected role", e);

                // Add player's ID to the list of players who have selected a role
                this.mutableGame.players_with_selected_roles.push(e.player.id);
                this.mutableGame.num_available_roles -= 1;

                // Switch to the main phase when all players have selected their roles
                if (this.mutableGame.players_with_selected_roles.length === this.mutableGame.players.length) {
                    setTimeout(function() {
                        this.phase = "main";
                    }.bind(this), 2000);
                }

            },
            onTurnEnded(e) {
                console.log(this.tag+"[event] turn ended", e);

                // Set the number of the player who's turn it is currently
                this.playersTurn = e.game.player_turn;
                
            },
            onRoundEnded(e) {
                console.log(this.tag+"[event] round ended", e);

            },
            onGameEnded(e) {
                console.log(this.tag+"[event] game ended", e);

            },
            onClickRoleCard(index) {
                console.log(this.tag+" clicked role card", index);

                // Save the index of the selected role card
                this.selectRole.selectedCardIndex = index;

                // Indicate we're doing stuff
                this.selectRole.loading = true;

                // Perform the action
                this.sendPerformActionRequest("selected_role_card", { index: index })

                    // If we succeeded to perform the action
                    .then(function(response) {
                        console.log(this.tag+" succeeded to select role!", response);

                        // Save the assigned role & the player's hand
                        this.mutablePlayerRole = response.data.role;
                        
                        // Initialize the player's hand
                        this.initializeHand(response.data.hand);

                        // Add player's id to the game's list of players with selected roles
                        this.mutableGame.players_with_selected_roles.push(this.mutablePlayer.id);
                        this.mutableGame.num_available_roles -= 1;

                        // Indicate we're done doing stuff
                        this.selectRole.loading = false;

                        // Switch to the main phase when all players have selected their roles
                        if (this.mutableGame.players_with_selected_roles.length === this.mutableGame.players.length) {
                            setTimeout(function() {
                                this.phase = "main";
                            }.bind(this), 2000);
                        }

                    }.bind(this))

                    // If we failed to perform the action; let's make the user be stuck here because wtf
                    .catch(function(error) {
                        console.warn(this.tag+" failed to select role!", error);
                    }.bind(this));

            },
            onClickHandCard(index) {
                console.log(this.tag+" clicked card in hand", index);
                this.mutableHand[index].selected = !this.mutableHand[index].selected;
            },
            onClickFoldCard() {
                console.log(this.tag+" clicked fold card button");

                // Start loading
                this.foldCard.loading = true;

                // Perform the action
                this.sendPerformActionRequest("fold_card", { index: this.selectedHandCardIndex })

                    // Action succeeded
                    .then(function(response) {
                        console.log(this.tag+" card fold succeeded", response);
                        
                        // Remove the card from the user's hand
                        this.mutableHand.splice(this.selectedHandCardIndex, 1);

                        let cardEntry = {
                            data: response.data.new_card,
                            selected: false,
                        };
                        console.log("card entry: ", cardEntry);
                        console.log(this.mutableHand.length);

                        // Add the drawn card to the user's hand
                        this.mutableHand.push(cardEntry);

                        console.log(this.mutableHand.length);

                        // Stop loading
                        this.foldCard.loading = false;

                    }.bind(this))

                    // Action failed
                    .catch(function(error) {
                        console.warn(this.tag+" failed to fold card!", error);

                        // Stop loading
                        this.foldCard.loading = false;

                    }.bind(this));

            },
            onClickPlayCard() {
                console.log(this.tag+" clicked play card button");



            },
            onClickedBoardTile(data) {
                console.log(this.tag+" clicked board tile: ", data);
                
            },
            sendPerformActionRequest(action, data) {
                return new Promise(function(resolve, reject) {
                    // Compose the payload to send to the API
                    let payload = new FormData();
                    payload.append("game_id", this.game.id);
                    payload.append("action", action);
                    payload.append("data", JSON.stringify(data));
                    // Send API request
                    this.axios.post(this.performActionApiEndpoint, payload)
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
            getRoleLabelById(id) {
                for (let i = 0; i < this.roles.length; i++) {
                    if (this.roles[i].id === id) {
                        return this.roles[i].label;
                    }
                }
                return "Unknown role";
            },
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
            #role-selection {
                flex: 1;
                padding: 30px;
                box-sizing: border-box;
                #role-assigned {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    .role-card {
                        margin: 15px 0 25px 0;
                    }
                }
                #role-not-assigned {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                }
                h1 {
                    text-align: center;
                }
                #role-selection__text {
                    margin: 0 0 30px 0;
                    text-align: center;
                }
                #available-roles {
                    margin: 0 0 30px 0;
                    h3 {
                        font-size: .9em;
                        text-align: center;
                        text-transform: uppercase;
                        color: rgba(255, 255, 255, 0.5);
                    }
                    #available-roles__list {
                        display: flex;
                        margin: 0 0 30px 0;
                        flex-direction: row;
                        justify-content: center;
                        .available-role__wrapper {
                            margin: 0 15px 0 0;
                            display: inline-block;
                            &:last-child {
                                margin: 0;
                            }
                            .available-role {
                                flex: 0;
                                display: flex;
                                font-size: .9em;
                                padding: 3px 8px;
                                border-radius: 3px;
                                flex-direction: row;
                                box-sizing: border-box;
                                background-color: hsl(0, 0%, 5%);
                                .role-name {}
                                .role-amount {
                                    margin: 0 0 0 5px;
                                }
                            }
                        }
                    }
                }
                #role-cards {
                    width: 100%;
                    h2 {
                        margin: 0 0 10px 0;
                        text-align: center;
                    }
                    h3 {
                        font-size: 1em;
                        text-align: center;
                        color: rgba(255, 255, 255, 0.75);
                    }
                    #role-cards__list {
                        display: flex;
                        flex-wrap: wrap;
                        flex-direction: row;
                        justify-content: center;
                        margin: 0 -15px -30px -15px;
                        .role-card__wrapper {
                            flex: 0 0 160px;
                            box-sizing: border-box;
                            padding: 0 15px 30px 15px;
                            
                        }
                    }
                }
                #role-card__selected {
                    #role-card__selected-content {
                        display: flex;
                        flex-direction: row;
                        justify-content: center;
                        #role-card__selected-card {
                            display: flex;
                            padding: 15px;
                            height: 200px;
                            color: #000;
                            flex: 0 0 130px;
                            margin: 0 30px 0 0;
                            border-radius: 3px;
                            position: relative;
                            align-items: center;
                            transition: all .3s;
                            box-sizing: border-box;
                            flex-direction: column;
                            justify-content: center;
                            background-color: rgba(255, 255, 255, 0.75);
                            #selected-card__title {
                                left: 0;
                                top: 15px;
                                width: 100%;
                                position: absolute;
                                text-align: center;
                            }
                            #selected-card__number {
                                font-size: 1.7em;
                                font-weight: 500;
                            }
                        }
                    }
                    #role-card__selected-text {
                        display: flex;
                        align-items: center;
                        flex-direction: column;
                        justify-content: center;
                        #selected-text__loading {
                            font-size: 2em;
                        }
                        #selected-text__title {
                            margin: 10px 0 0 0;
                        }
                    }
                }
            }
            #game-ui {
                height: 100%;
                display: flex;
                flex-direction: column;
                #board-area {
                    flex: 1;
                    position: relative;
                    #game-info {
                        top: 20px;
                        left: 25px;
                        position: absolute;
                        #game-info__current-round {
                            font-size: 2em;
                        }
                        #game-info__player-turn {
                            #my-turn {
                                font-weight: 500;
                                color: #ffd900;
                            }
                            #not-my-turn {
                                span {
                                    color: #ffd900;
                                    // text-decoration: underline;
                                }
                            }
                        }
                    }
                    #game-board__wrapper {
                        width: 100%;
                        height: 100%;
                        position: relative;
                    }
                }
                #action-area {
                    display: flex;
                    padding: 30px;
                    flex: 0 0 250px;
                    flex-direction: row;
                    box-sizing: border-box;
                    background-color: hsl(0, 0%, 2%);
                    #my-role {
                        flex: 0 0 130px;
                        margin: 0 30px 0 0;
                        #my-role__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            margin: 0 0 15px 0;
                            text-align: center;
                            text-transform: uppercase;
                        }
                        #my-role__card {
                            width: 130px;
                            height: 200px;
                            color: #000000;
                            border-radius: 3px;
                            position: relative;
                            background-color: hsl(0, 0%, 95%);
                            #my-role__card-text {
                                left: 0;
                                bottom: 0;
                                width: 100%;
                                padding: 15px 0;
                                text-align: center;
                                position: absolute;
                                box-sizing: border-box;
                            }
                        }
                    }
                    #my-hand {
                        flex: 1;
                        display: flex;
                        margin: 0 30px 0 0;
                        flex-direction: column;
                        justify-content: center;
                        #my-hand__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            margin: 0 0 15px 0;
                            text-align: center;
                            text-transform: uppercase;
                        }
                        #my-hand__no-cards {
                            display: flex;
                            flex-direction: row;
                            align-items: center;
                            justify-content: center;
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
                                }
                            }
                        }
                    }
                    #my-actions {
                        flex: 0 0 300px;
                        #my-actions__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            text-align: right;
                            margin: 0 0 15px 0;
                            text-transform: uppercase;
                        }
                        #my-actions__list {
                            width: 100%;
                            .action {
                                margin: 0 0 15px 0;
                                &:last-child {
                                    margin: 0;
                                }
                            }
                        }
                        #my-actions__too-many-cards {
                            text-align: right;
                        }
                        #my-actions__select-card {
                            text-align: right;
                        }
                        #my-actions__wait {
                            text-align: right;
                        }
                    }
                }
            }
        }
        #game-sidebar {
            display: flex;
            flex: 0 0 350px;
            flex-direction: column;
            background-color: hsl(0, 0%, 5%);
            #game-players__wrapper {
                flex: 1;
            }
            #game-chat__wrapper {
                flex: 0 0 300px;
            }
        }
    }
    .role-card {
        width: 130px;
        display: flex;
        padding: 15px;
        height: 200px;
        color: #000;
        border-radius: 3px;
        position: relative;
        align-items: center;
        transition: all .3s;
        box-sizing: border-box;
        flex-direction: column;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.75);
        &:hover {
            &.no-hover {
                cursor: default;
                background-color: rgba(255, 255, 255, 0.75);
            }
            cursor: pointer;
            background-color: rgba(255, 255, 255, 1);
            .role-card__select {
                opacity: 1;
            }
        }
        .role-card__title {
            left: 0;
            top: 15px;
            width: 100%;
            position: absolute;
            text-align: center;
        }
        .role-card__number {
            font-size: 1.7em;
            font-weight: 500;
        }
        .role-card__select {
            left: 0;
            opacity: 0;
            width: 100%;
            bottom: 15px;
            font-weight: 500;
            text-align: center;
            position: absolute;
            transition: all .3s;
        }
    }
</style>