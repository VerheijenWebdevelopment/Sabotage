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

                    <!-- Mode bar -->
                    <div id="game-ui__mode-wrapper" v-if="showModeBar">
                        <!-- Select gold location mode -->
                        <div id="game-ui__mode" class="elevation-1" v-if="modes.select_gold_location">
                            Selecteer de goud locatie die je wilt bekijken.
                        </div>
                        <!-- Collapse tunnel mode -->
                        <div id="game-ui__mode" class="elevation-1" v-if="modes.select_tunnel">
                            Selecteer de tunnel die je in wilt laten storten. Jij boefje.
                        </div>
                        <!-- Place tunnel mode -->
                        <div id="game-ui__mode" class="elevation-1" v-if="modes.select_tile">
                            Selecteer de tegel waar je de tunnel wilt plaatsen.
                        </div>
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
                            <div class="card-action" v-if="showCardActionPlay">
                                <v-btn small dark @click="onClickPlayCard">
                                    <span>
                                        <i class="fas fa-long-arrow-alt-up"></i>
                                    </span>
                                    Speel kaart
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="showCardActionFoldCard">
                                <v-btn small dark @click="onClickFoldCard">
                                    <span>
                                        <i class="far fa-times-circle"></i>
                                    </span>
                                    Kaart afleggen
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="showCardActionFoldCards">
                                <v-btn small dark @click="onClickFoldCards">
                                    <span>
                                        <i class="far fa-times-circle"></i>
                                    </span>
                                    Kaarten afleggen
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="showCardActionFoldCardsUnblock">
                                <v-btn small dark @click="onClickFoldCardsUnblock">
                                    <span>
                                        <i class="far fa-times-circle"></i>
                                    </span>
                                    Kaarten afleggen & deblokkeren
                                </v-btn>
                            </div>
                            <div class="card-action" v-if="numSelectedHandCards === 1">
                                <v-btn small dark class="icon-only" @click="onClickViewCard">
                                    <i class="far fa-eye"></i>
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
        <v-dialog v-model="dialogs.view_card.show" width="400">
            <div class="dialog dark" v-if="dialogs.view_card.index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelViewCard">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Kaart bekijken</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="card mb-15 ma" :style="{ backgroundImage: 'url('+viewCardDialogCard.image_url+')' }"></div>
                        <div class="card-description">
                            {{ viewCardDialogCard.description }}
                        </div>
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
        <v-dialog v-model="dialogs.fold_cards_unblock.show" width="800">
            <div class="dialog dark" v-if="dialogs.fold_cards_unblock.indices.length > 0">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelFoldCardsUnblock">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Kaarten afleggen & gereedschap deblokkeren</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="cards">
                            <div class="card mb-15" v-for="(card, ci) in foldCardsUnblockDialogCards" :key="ci" :style="{ backgroundImage: 'url('+card.image_url+')' }"></div>
                        </div>
                        <!-- Select tool -->
                        <div class="select-tool">
                            <div class="select-tool__title">
                                Selecteer het gereedschap dat je wilt herstellen door de bovenstaande kaarten af te leggen.<br>
                                Je krijgt er maar 1 kaart voor terug.
                            </div>
                            <div class="select-tool__list" v-if="foldCardsUnblockDialogToolOptions.length > 0">
                                <div class="select-tool__list-item" v-for="(tool, ti) in foldCardsUnblockDialogToolOptions" :key="ti">
                                    <div class="tool-option" :class="{ selected: dialogs.fold_cards_unblock.tool === tool }" @click="onClickSelectTool('fold_cards_unblock', tool)">
                                        {{ tool }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-box" v-if="foldCardsUnblockDialogToolOptions.length === 0">
                                Er is geen gereedschap geblokkeerd op dit moment!
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelFoldCardsUnblock">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark color="red" @click="onClickConfirmFoldCardsUnblock" :loading="dialogs.fold_cards_unblock.loading" :disabled="foldCardsUnblockDialogConfirmDisabled">
                            <i class="fas fa-recycle"></i>
                            Kaarten afleggen & deblokkeren
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Sabotage player dialog -->
        <v-dialog v-model="dialogs.sabotage.show" width="600">
            <div class="dialog dark" v-if="dialogs.sabotage.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelSabotage">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">{{ sabotageDialogTitle }}</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.sabotage.player_id === player.id }" @click="onClickSelectPlayer('sabotage', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Select tool -->
                    <div class="select-tool" v-if="sabotageDialogShowToolSelection">
                        <div class="select-tool__title">Selecteer gereedschapschap dat je wilt blokkeren</div>
                        <div class="select-tool__list">
                            <div class="select-tool__list-item" v-for="(tool, ti) in sabotageDialogToolOptions" :key="ti">
                                <div class="tool-option" :class="{ selected: dialogs.sabotage.tool === tool }" @click="onClickSelectTool('sabotage', tool)">
                                    {{ tool }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelSabotage">
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmSabotage" :loading="dialogs.sabotage.loading" :disabled="sabotageDialogDisableSubmit">
                            Saboteer gereedschap
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Recover player dialog -->
        <v-dialog v-model="dialogs.recover.show" width="600">
            <div class="dialog dark" v-if="dialogs.recover.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelRecover">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">{{ recoverDialogTitle }}</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayers" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.recover.player_id === player.id }" @click="onClickSelectPlayer('recover', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Select tool -->
                    <div class="select-tool" v-if="recoverDialogShowToolSelection">
                        <div class="select-tool__title">Selecteer gereedschap dat je wilt herstellen</div>
                        <div class="select-tool__list">
                            <div class="select-tool__list-item" v-for="(tool, ti) in recoverDialogToolOptions" :key="ti">
                                <div class="tool-option" :class="{ selected: dialogs.recover.tool === tool }" @click="onClickSelectTool('recover', tool)">
                                    {{ tool }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelRecover">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmRecover" :loading="dialogs.recover.loading" :disabled="recoverDialogDisableSubmit">
                            Herstel speler's gereedschap
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>
        
        <!-- Imprison player dialog -->
        <v-dialog v-model="dialogs.imprison.show" width="600">
            <div class="dialog dark" v-if="dialogs.imprison.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelImprison">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Speler in de gevangenis stoppen</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.imprison.player_id === player.id }" @click="onClickSelectPlayer('imprison', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelImprison">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmImprison" :loading="dialogs.imprison.loading" :disabled="imprisonDialogDisableSubmit">
                            Naar de gevangenis!
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Free player dialog -->
        <v-dialog v-model="dialogs.free.show" width="600">
            <div class="dialog dark" v-if="dialogs.free.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelFree">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Speler bevrijden uit de gevangenis</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayers" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.free.player_id === player.id }" @click="onClickSelectPlayer('free', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelFree">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmFree" :loading="dialogs.free.loading" :disabled="freeDialogDisableSubmit">
                            Bevrijd speler
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Thief dialog -->
        <v-dialog v-model="dialogs.thief.show" width="600">
            <div class="dialog dark" v-if="dialogs.thief.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelThief">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Dief kaart spelen</div>
                    <!-- Text -->
                    <div class="dialog-text">{{ thiefDialogCard.description }}</div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelThief">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmThief" :loading="dialogs.thief.loading">
                            Dief kaart spelen
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Dont touch dialog -->
        <v-dialog v-model="dialogs.dont_touch.show" width="600">
            <div class="dialog dark" v-if="dialogs.dont_touch.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelDontTouch">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Swieber niet stelen!</div>
                    <!-- Text -->
                    <div class="dialog-text">{{ dontTouchDialogCard.description }}</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.dont_touch.player_id === player.id }" @click="onClickSelectPlayer('dont_touch', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelDontTouch">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmDontTouch" :loading="dialogs.dont_touch.loading" :disabled="dontTouchDialogDisableSubmit">
                            Aflbijven!
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Enlighten dialog -->
        <v-dialog v-model="dialogs.enlighten.show" width="700">
            <div class="dialog dark" v-if="dialogs.enlighten.card_index !== null && dialogs.enlighten.gold_location !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelEnlighten">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Plan kaart spelen</div>
                    <!-- Text -->
                    <div class="dialog-text">
                        {{ enlightenDialogCard.description }}
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelEnlighten">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmEnlighten">
                            Selecteer goud locatie
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm (enlighten) gold location dialog -->
        <v-dialog v-model="dialogs.confirm_enlighten.show" width="800">
            <div class="dialog dark" v-if="dialogs.confirm_enlighten.card_index !== null && dialogs.confirm_enlighten.gold_location !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelConfirmEnlighten">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Goud locatie bekijken</div>
                    <!-- Text -->
                    <div class="dialog-text">
                        {{ confirmEnlightenText }}<br>
                        Weet je zeker dat je deze goud locatie wilt bekijken?
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelConfirmEnlighten">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                        <v-btn text dark @click="onClickRetryConfirmEnlighten">
                            <i class="fas fa-recycle"></i>
                            Andere locatie selecteren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmConfirmEnlighten" :loading="dialogs.confirm_enlighten.loading">
                            <i class="far fa-eye"></i>
                            Bekijk goud locatie
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Gold location dialog (result of confirm enlighten dialog) -->
        <v-dialog v-model="dialogs.reveal_gold_location.show" width="500">
            <div class="dialog dark" v-if="dialogs.reveal_gold_location.gold_location !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.reveal_gold_location.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Goud Locatie #{{ dialogs.reveal_gold_location.gold_location }}</div>
                    <!-- Does contain gold text -->
                    <div class="dialog-text" v-if="dialogs.reveal_gold_location.contains_gold">
                        <div id="reveal-gold-location">
                            <div id="reveal-gold-location__image" :style="{ backgroundImage: 'url('+icons.gold_bars+')' }"></div>
                            <div id="reveal-gold-location__text">
                                Het goud bevindt zich hier!
                            </div>
                        </div>
                    </div>  
                    <!-- Does not contain gold text -->
                    <div class="dialog-text" v-if="!dialogs.reveal_gold_location.contains_gold">
                        <div id="reveal-gold-location">
                            <div id="reveal-gold-location__image" :style="{ backgroundImage: 'url('+icons.coal+')' }"></div>
                            <div id="reveal-gold-location__text">
                                Hier bevindt het goud zich niet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Inspection dialog -->
        <v-dialog v-model="dialogs.inspection.show" width="600">
            <div class="dialog dark" v-if="dialogs.inspection.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelImprison">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Inspectie kaart spelen</div>
                    <!-- Description -->
                    <div class="dialog-text">Bekijk de rol van een speler naar keuze.</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.inspection.player_id === player.id }" @click="onClickSelectPlayer('inspection', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelInspection">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmInspection" :loading="dialogs.inspection.loading" :disabled="inspectionDialogDisableSubmit">
                            Inspecteer speler
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Reveal role dialog -->
        <v-dialog v-model="dialogs.reveal_role.show" width="500">
            <div class="dialog dark" v-if="dialogs.reveal_role.player_id !== null && dialogs.reveal_role.role !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelRevealRole">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Rol van {{ revealRoleDialogPlayer.user.username  }}</div>
                    <!-- Text -->
                    <div class="dialog-text" v-html="revealRoleDialogText"></div>
                </div>
            </div>
        </v-dialog>

        <!-- Exchange hands dialog -->
        <v-dialog v-model="dialogs.exchange_hands.show" width="600">
            <div class="dialog dark" v-if="dialogs.exchange_hands.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelExchangeHands">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Handen ruilen</div>
                    <!-- Text -->
                    <div class="dialog-text">Verwissel jouw hand met die van een medespeler.</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.exchange_hands.player_id === player.id }" @click="onClickSelectPlayer('exchange_hands', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelExchangeHands">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmExchangeHands" :loading="dialogs.exchange_hands.loading" :disabled="exchangeHandsDialogDisableSubmit">
                            Handen wisselen
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Exchange hands completed dialog -->
        <v-dialog v-model="dialogs.exchange_hands_completed.show" width="500">
            <div class="dialog dark" v-if="dialogs.exchange_hands_completed.other_player_id !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.exchange_hands_completed.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Handen verwisseld</div>
                    <!-- Text -->
                    <div class="dialog-text">Je hebt je hand moeten afstaan aan <strong>{{ exchangeHandsCompletedOtherPlayer }}</strong> en hebt zijn/haar kaarten daarvoor in de plaats gekregen. Daarnaast heb je ook een nieuwe kaart getrokken. Lucky you.</div>
                </div>
            </div>
        </v-dialog>

        <!-- Exchange hats dialog -->
        <v-dialog v-model="dialogs.exchange_hats.show" width="600">
            <div class="dialog dark" v-if="dialogs.exchange_hats.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelExchangeHats">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Mutsen ruilen</div>
                    <!-- Text -->
                    <div class="dialog-text">De gekozen speler moet zijn rolkaart afstaan, waarop hij of zij een nieuwe rol krijgt toegewezen.</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Selecteer je doelwit</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.exchange_hats.player_id === player.id }" @click="onClickSelectPlayer('exchange_hats', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelExchangeHats">
                            <i class="fas fa-arrow-left"></i>
                            Annuleren
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmExchangeHats" :loading="dialogs.exchange_hats.loading" :disabled="exchangeHatsDialogDisableSubmit">
                            Mutsen verwisselen
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Exchange hats completed dialog -->
        <v-dialog v-model="dialogs.exchange_hats_completed.show" width="500">
            <div class="dialog dark" v-if="dialogs.exchange_hats_completed.other_player_id !== null && mutablePlayerRole !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.exchange_hats_completed.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Mutsen verwisseld</div>
                    <!-- Text -->
                    <div class="dialog-text">Je hebt je muts moeten afdoen van <strong>{{ exchangeHatsCompletedOtherPlayer }}</strong>.<br>Je hebt een nieuwe rol toegewezen gekregen:</div>
                    <!-- Role -->
                    <div id="new-role">
                        <div id="new-role__name">
                            {{ mutablePlayerRole.label }}
                            <span class="blue-text" v-if="mutablePlayerRole.name === 'blue_digger'">Blauwe team</span>    
                            <span class="blue-text" v-if="mutablePlayerRole.name === 'green_digger'">Groene team</span>    
                        </div>
                        <div id="new-role__description">{{ mutablePlayerRole.description }}</div>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Collapse tunnel dialog -->
        <v-dialog v-model="dialogs.collapse.show" width="600">

        </v-dialog>

        <!-- Place tunnel dialog -->
        <v-dialog v-model="dialogs.place_tunnel.show" width="600">

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
                view_card: {
                    show: false,
                    index: null,
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
                sabotage: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    tool: null,
                    loading: false,
                },
                recover: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    tool: null,
                    loading: false,
                },
                imprison: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    loading: false,
                },
                free: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    loading: false,
                },
                thief: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    loading: false,
                },
                dont_touch: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    loading: false,
                },
                enlighten: {
                    show: false,
                    card_index: null,
                },
                confirm_enlighten: {
                    show: false,
                    loading: false,
                    card_index: null,
                    gold_location: null,
                },
                reveal_gold_location: {
                    show: false,
                    gold_location: null,
                    contains_gold: false,
                },
                inspection: {
                    show: false,
                    loading: false,
                    card_index: null,
                    player_id: null,
                },
                reveal_role: {
                    show: false,
                    player_id: null,
                    role: null,
                },
                collapse: {
                    show: false,
                    card_index: null,
                    tile_coordinates: null,
                },
                place_tunnel: {
                    show: false,
                    card_index: null,
                    tile_coordinates: null,
                },
                exchange_hands: {
                    show: false,
                    loading: false,
                    card_index: null,
                    player_id: null,
                },
                exchange_hands_completed: {
                    show: false,
                    other_player_id: null,
                },
                exchange_hats: {
                    show: false,
                    loading: false,
                    card_index: null,
                    player_id: null,
                },
                exchange_hats_completed: {
                    show: false,
                    other_player_id: null,
                },
            },
            modes: {
                select_gold_location: false,
                select_tunnel: false,
                select_tile: false,
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
            // Players
            mutablePlayersExcludingMe() {
                let out = [];
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id !== this.mutablePlayer.id) {
                        out.push(this.mutablePlayers[i]);
                    }
                }
                return out;
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
            showCardActionPlay() {
                if (this.numSelectedHandCards === 1 && this.itsMyTurn) {
                    if (this.selectedHandCard.card.type === "tunnel") {
                        return this.mutablePlayer.pickaxe_available && 
                               this.mutablePlayer.light_available && 
                               this.mutablePlayer.cart_available &&
                               !this.mutablePlayer.in_jail;
                    } else {
                        return true;
                    }
                }
                return false;
            },
            showCardActionFoldCard() {
                return this.numSelectedHandCards === 1 && this.itsMyTurn;
            },
            showCardActionFoldCards() {
                return this.numSelectedHandCards > 1 && this.itsMyTurn;
            },
            showCardActionFoldCardsUnblock() {
                return this.numSelectedHandCards === 2 && this.itsMyTurn;
            },
            // UI
            showModeBar() {
                return this.modes.select_gold_location || this.modes.select_tunnel || this.modes.select_tile;
            },
            // View card dialog
            viewCardDialogCard() {
                if (this.dialogs.view_card.index !== null) {
                    return this.mutableHand[this.dialogs.view_card.index].card;
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
            foldCardsUnblockDialogCards() {
                let out = [];
                for (let i = 0; i < this.dialogs.fold_cards_unblock.indices.length; i++) {
                    out.push(this.mutableHand[this.dialogs.fold_cards_unblock.indices[i]].card);
                }
                return out;
            },
            foldCardsUnblockDialogToolOptions() {
                let out = [];
                if (!this.mutablePlayer.pickaxe_available) out.push("pickaxe");
                if (!this.mutablePlayer.light_available) out.push("light");
                if (!this.mutablePlayer.cart_available) out.push("cart");
                return out;
            },
            foldCardsUnblockDialogConfirmDisabled() {
                return this.dialogs.fold_cards_unblock.tool === null;
            },
            // Sabotage dialog
            sabotageDialogCard() {
                if (this.dialogs.sabotage.card_index !== null) {
                    return this.mutableHand[this.dialogs.sabotage.card_index].card;
                }
                return false;
            },
            sabotageDialogTitle() {
                if (this.dialogs.sabotage.card_index !== null) {
                    let out = "Saboteer speler's ";
                    let card = this.mutableHand[this.dialogs.sabotage.card_index].card;
                    if (card.name === "sabotage_pickaxe") {
                        out += "pickaxe";
                    } else if (card.name === "sabotage_light") {
                        out += "lantaarn";
                    } else if (card.name === "sabotage_cart") {
                        out += "minecart";
                    } else {
                        out += "gereedschap";
                    }
                    console.log("title: ", out, card.name);
                    return out;
                }
                return "";
            },
            sabotageDialogDisableSubmit() {
                let card = this.mutableHand[this.dialogs.sabotage.card_index];
                if (card) {
                    if (card.name === "sabotage_pickaxe_light" || card.name === "sabotage_pickaxe_cart" || card.name === "sabotage_light_cart") {
                        return this.dialogs.sabotage.player_id === null || this.dialogs.sabotage.tool === null ? true : false;
                    } else {
                        return this.dialogs.sabotage.player_id === null ? true : false;
                    }
                }
                return true;
            },
            sabotageDialogShowToolSelection() {
                let sabotageCard = this.mutableHand[this.dialogs.sabotage.card_index];
                if (sabotageCard) {
                    return sabotageCard.card.name === "sabotage_pickaxe_light" || sabotageCard.card.name === "sabotage_pickaxe_axe" || sabotageCard.card.name === "sabotage_light_cart";
                }
                return false;
            },
            sabotageDialogToolOptions() {
                let out = [];
                let sabotageCard = this.mutableHand[this.dialogs.sabotage.card_index];
                if (sabotageCard) {
                    if (sabotageCard.card.name === "sabotage_pickaxe_light") {
                        out.push("pickaxe");
                        out.push("light");
                    } else if (sabotageCard.card.name === "sabotage_pickaxe_cart") {
                        out.push("pickaxe");
                        out.push("cart");
                    } else {
                        out.push("light");
                        out.push("cart");
                    }
                }
                return out;
            },
            // Recover dialog
            recoverDialogCard() {
                if (this.dialogs.recover.card_index !== null) {
                    return this.mutableHand[this.dialogs.recover.card_index].card;
                }
                return false;
            },
            recoverDialogTitle() {
                if (this.dialogs.recover.card_index !== null) {
                    let out = "Herstel speler's ";
                    let card = this.mutableHand[this.dialogs.recover.card_index].card;
                    if (card.name === "recover_pickaxe") {
                        out += "pickaxe";
                    } else if (card.name === "recover_light") {
                        out += "light";
                    } else if (card.name === "recover_cart") {
                        out += "cart";
                    } else {
                        out += "gereedschap";
                    }
                    return out;
                }
                return "";
            },
            recoverDialogDisableSubmit() {
                let recoverCard = this.mutableHand[this.dialogs.recover.card_index];
                if (recoverCard) {
                    if (recoverCard.card.name === "recover_pickaxe_light" || recoverCard.card.name === "recover_pickaxe_cart" || recoverCard.card.name === "recover_light_cart") {
                        return this.dialogs.recover.player_id === null || this.dialogs.recover.tool === null ? true : false;
                    } else {
                        return this.dialogs.recover.player_id === null ? true : false;
                    }
                }
                return true;
            },
            recoverDialogShowToolSelection() {
                let recoverCard = this.mutableHand[this.dialogs.recover.card_index];
                if (recoverCard) {
                    return recoverCard.card.name === "recover_pickaxe_light" || recoverCard.card.name === "recover_pickaxe_cart" || recoverCard.card.name === "recover_light_cart";
                }
                return false;
            },
            recoverDialogToolOptions() {
                let out = [];
                let recoverCard = this.mutableHand[this.dialogs.recover.card_index];
                if (recoverCard) {
                    if (recoverCard.card.name === "recover_pickaxe_light") {
                        out.push("pickaxe");
                        out.push("light");
                    } else if (recoverCard.card.name === "recover_pickaxe_cart") {
                        out.push("pickaxe");
                        out.push("cart");
                    } else {
                        out.push("light");
                        out.push("cart");
                    }
                }
                return out;
            },
            // Imprison dialog
            imprisonDialogCard() {
                if (this.dialogs.imprison.card_index !== null) {
                    return this.mutableHand[this.dialogs.imprison.card_index].card;
                }
                return false;
            },
            imprisonDialogDisableSubmit() {
                return this.dialogs.imprison.player_id === null ? true : false;
            },
            // Free dialog
            freeDialogCard() {
                if (this.dialogs.free.card_index !== null) {
                    return this.mutableHand[this.dialogs.free.card_index].card;
                }
                return false;
            },
            freeDialogDisableSubmit() {
                return this.dialogs.free.player_id === null ? true : false;
            },
            // Thief dialog
            thiefDialogCard() {
                if (this.dialogs.thief.card_index !== null) {
                    return this.mutableHand[this.dialogs.thief.card_index].card;
                }
                return false;
            },
            // Dont touch dialog
            dontTouchDialogCard() {
                if (this.dialogs.dont_touch.card_index !== null) {
                    return this.mutableHand[this.dialogs.dont_touch.card_index].card;
                }
                return false;
            },
            dontTouchDialogDisableSubmit() {
                return this.dialogs.dont_touch.player_id === null ? true : false;
            },
            // Enlighten dialog
            enlightenDialogCard() {
                if (this.dialogs.enlighten.card_index !== null) {
                    return this.mutableHand[this.dialogs.enlighten.card_index].card;
                }
                return false;
            },
            // Confirm enlighten dialog
            confirmEnlightenDialogCard() {
                if (this.dialogs.confirm_enlighten.card_index !== null) {
                    return this.mutableHand[this.dialogs.confirm_enlighten.card_index].card;
                }
                return false;
            },
            confirmEnlightenText() {
                let out = "Je hebt de ";
                if (this.dialogs.confirm_enlighten.gold_location === 1) {
                    out += "eerste (bovenste)";
                } else if (this.dialogs.confirm_enlighten.gold_location === 2) {
                    out += "tweede (middelste)";
                } else {
                    out += "laatste (onderste)"
                }
                out += " goud locatie gekozen.";
                return out;
            },
            // Inspection dialog
            inspectionDialogDisableSubmit() {
                return this.dialogs.inspection.player_id === null;
            },
            // Reveal role dialog
            revealRoleDialogPlayer() {
                if (this.dialogs.reveal_role.player_id !== null) {
                    for (let i = 0; i < this.mutablePlayers.length; i++) {
                        if (this.mutablePlayers[i].id === this.dialogs.reveal_role.player_id) {
                            return this.mutablePlayers[i];
                        }
                    }
                }
                return false;
            },
            revealRoleDialogText() {
                if (this.dialogs.reveal_role.role.name === "green_digger") {
                    return this.revealRoleDialogPlayer.user.username+" is een <strong>Goudzoeker</strong> uit <span class='green-text'>Team Groen</span>.";
                } else if (this.dialogs.reveal_role.role.name === "blue_digger") {
                    return this.revealRoleDialogPlayer.user.username+" is een <strong>Goudzoeker</strong> uit <span class='blue-text'>Team Blauw</span>.";
                } else {
                    return this.revealRoleDialogPlayer.user.username+" is een <strong>"+this.dialogs.reveal_role.role.label+"</strong>.";
                }
            },
            // Exchange hands dialog
            exchangeHandsDialogCard() {
                if (this.dialogs.exchange_hands.card_index !== null) {
                    return this.mutableHand[this.dialogs.exchange_hands.card_index].card;
                }
                return false;
            },
            exchangeHandsDialogDisableSubmit() {
                return this.dialogs.exchange_hands.player_id === null;
            },
            // Exchange hands completed dialog
            exchangeHandsCompletedOtherPlayer() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === this.dialogs.exchange_hands_completed.other_player_id) {
                        return this.mutablePlayers[i].user.username;
                    }
                }
                return false;
            },
            // Exchange hats dialog
            exchangeHatsDialogCard() {
                if (this.dialogs.exchange_hats.card_index !== null) {
                    return this.mutableHand[this.dialogs.exchange_hats.card_index].card;
                }
                return false;
            },
            exchangeHatsDialogDisableSubmit() {
                return this.dialogs.exchange_hats.player_id === null;
            },
            // Exchange hats completed dialog
            exchangeHatsCompletedOtherPlayer() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === this.dialogs.exchange_hats_completed.other_player_id) {
                        return this.mutablePlayers[i].user.username;
                    }
                }
                return false;
            },
            // Collapse tunnel dialog
            collapseDialogCard() {
                if (this.dialogs.collapse.card_index !== null) {
                    return this.mutableHand[this.dialogs.collapse.card_index].card;
                }
                return false;
            },
            // Place tunnel dialog
            placeTunnelDialogCard() {
                if (this.dialogs.place_tunnel.card_index !== null) {
                    return this.mutableHand[this.dialogs.place_tunnel.card_index].card;
                }
                return false;
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
                    .listen('Game\\PlayerImprisoned', this.onPlayerImprisoned)
                    .listen('Game\\PlayerFreed', this.onPlayerFreed)
                    .listen('Game\\PlayerIsThief', this.onPlayerIsThief)
                    .listen('Game\\PlayerNoLongerThief', this.onPlayerNoLongerThief)
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

                // Player channel events
                Echo.private('player.'+this.player.id)
                    .listen('Game\\PlayerRoleChanged', this.onPlayerRoleChanged)
                    .listen('Game\\PlayerHandChanged', this.onPlayerHandChanged);

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

                // Update the target player's tool's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        if (e.tool === "pickaxe") {
                            this.mutablePlayers[i].pickaxe_available = 0;
                        } else if (e.tool === "light") {
                            this.mutablePlayers[i].light_available = 0;
                        } else if (e.tool === "cart") {
                            this.mutablePlayers[i].cart_available = 0;
                        }
                        break;
                    }
                }

                // If we were the target player, update our mutablePlayer as well
                if (e.target_player.id === this.mutablePlayer.id) {
                    if (e.tool === "pickaxe") {
                        this.mutablePlayer.pickaxe_available = 0;
                    } else if (e.tool === "light") {
                        this.mutablePlayer.light_available = 0;
                    } else if (e.tool === "cart") {
                        this.mutablePlayer.cart_available = 0;
                    }
                }

            },
            onPlayerToolRecovered(e) {
                console.log(this.tag+"[event] received event player tool recovered:", e);

                // Update the target player's tool's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        if (e.tool === "pickaxe") {
                            this.mutablePlayers[i].pickaxe_available = 1;
                        } else if (e.tool === "light") {
                            this.mutablePlayers[i].light_available = 1;
                        } else if (e.tool === "cart") {
                            this.mutablePlayers[i].cart_available = 1;
                        }
                        break;
                    }
                }

                // If we were the target player, update our mutablePlayer as well
                if (e.target_player.id === this.mutablePlayer.id) {
                    if (e.tool === "pickaxe") {
                        this.mutablePlayer.pickaxe_available = 1;
                    } else if (e.tool === "light") {
                        this.mutablePlayer.light_available = 1;
                    } else if (e.tool === "cart") {
                        this.mutablePlayer.cart_available = 1;
                    }
                }

            },
            onPlayerImprisoned(e) {
                console.log(this.tag+"[event] received event player was imprisoned:", e);

                // Update player's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        this.mutablePlayers[i].in_jail = 1;
                        break;
                    }
                }

                // If we were the target player, update mutable player as well
                if (e.target_player.id === this.mutablePlayer.id) {
                    this.mutablePlayer.in_jail = 1;
                }

            },
            onPlayerFreed(e) {
                console.log(this.tag+"[event] received event player was freed from prison:", e);

                // Update player's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        this.mutablePlayers[i].in_jail = 0;
                        break;
                    }
                }

                // If we were the target player, update mutable player as well
                if (e.target_player.id === this.mutablePlayer.id) {
                    this.mutablePlayer.in_jail = 0;
                }
                
            },
            onPlayerIsThief(e) {
                console.log(this.tag+"[event] received event player is thief:", e);

                // Update player's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.player.id) {
                        this.mutablePlayers[i].thief_activated = 1;
                        break;
                    }
                }

                // If we were the target player, update mutable player as well
                if (e.player.id === this.mutablePlayer.id) {
                    this.mutablePlayer.thief_activated = 1;
                }
                
            },
            onPlayerNoLongerThief(e) {
                console.log(this.tag+"[event] received event player is no longer thief:", e);

                // Update player's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        this.mutablePlayers[i].thief_activated = 0;
                        break;
                    }
                }

                // If we were the target player, update mutable player as well
                if (e.target_player.id === this.mutablePlayer.id) {
                    this.mutablePlayer.thief_activated = 0;
                }l
                
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
            onPlayerRoleChanged(e) {
                console.log(this.tag+"[event] received player role changed event", e);
                // Update the player's role
                this.mutablePlayerRole = e.role;
                // Populate & show the exchange hats completed dialog
                this.dialogs.exchange_hats_completed.other_player_id = e.other_player.id;
                this.dialogs.exchange_hats_completed.show = true;
            },
            onPlayerHandChanged(e) {
                console.log(this.tag+"[event] received player hand changed event", e);
                // Update the player's hand
                this.initializeHand(e.hand);
                // Populate & show the exchange hands completed dialog
                this.dialogs.exchange_hands_completed.other_player_id = e.other_player.id;
                this.dialogs.exchange_hands_completed.show = true;
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

                // If we're in gold location selection mode
                if (this.modes.select_gold_location) {

                    // Determine the ID of the start card
                    let startCardId = this.getCardIdByName("start");
                    if (startCardId) {
                        
                        // Determine the coordinates of the start card
                        let startCoordinates = null;
                        for (let y = 0; y < this.mutableRound.board.length; y++) {
                            if (startCoordinates !== null) break;
                            for (let x = 0; x < this.mutableRound.board[y].length; x++) {
                                if (this.mutableRound.board[y][x] !== null && this.mutableRound.board[y][x].card_id === startCardId) {
                                    startCoordinates = { x: x, y: y };
                                    break;
                                }
                            }
                        }

                        // Use the start card coordinates to determine the gold location card's coordinates
                        let goldLocationOne = { x: startCoordinates.x + 8, y: startCoordinates.y - 2 };
                        let goldLocationTwo = { x: startCoordinates.x + 8, y: startCoordinates.y };
                        let goldLocationThree = { x: startCoordinates.x + 8, y: startCoordinates.y + 2 };

                        // If the first gold location was selected
                        if (e.rowIndex === goldLocationOne.y && e.columnIndex === goldLocationOne.x) {
                            this.modes.select_gold_location = false;
                            this.dialogs.confirm_enlighten.gold_location = 1;
                            this.dialogs.confirm_enlighten.show = true;
                        }
                        // If the second gold location was selected
                        else if (e.rowIndex === goldLocationTwo.y && e.columnIndex === goldLocationTwo.x) {
                            this.modes.select_gold_location = false;
                            this.dialogs.confirm_enlighten.gold_location = 2;
                            this.dialogs.confirm_enlighten.show = true;
                        }
                        // If the third gold location was selected
                        else if (e.rowIndex === goldLocationThree.y && e.columnIndex === goldLocationThree.x) {
                            this.modes.select_gold_location = false;
                            this.dialogs.confirm_enlighten.gold_location = 3;
                            this.dialogs.confirm_enlighten.show = true;
                        }
                    }
                }

            },
            // Hand actions
            onClickPlayCard() {
                console.log(this.tag+" clicked play card button");
                // Grab the card's index & data
                let card, index;
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        index = i;
                        card = this.mutableHand[i].card;
                        break;
                    }
                }
                // Show appropriate dialog depending on the selected card
                if (card.type === "tunnel") {
                    this.dialogs.place_tunnel.card_index = index;
                    this.dialogs.place_tunnel.show = true;
                } else {
                    if (card.name.includes("sabotage")) {
                        this.dialogs.sabotage.card_index = index;
                        this.dialogs.sabotage.show = true;
                    } else if (card.name.includes("recover")) {
                        this.dialogs.recover.card_index = index;
                        this.dialogs.recover.show = true;
                    } else if (card.name === "imprison") {
                        this.dialogs.imprison.card_index = index;
                        this.dialogs.imprison.show = true;
                    } else if (card.name === "free") {
                        this.dialogs.free.card_index = index;
                        this.dialogs.free.show = true;
                    } else if (card.name === "thief") {
                        this.dialogs.thief.card_index = index;
                        this.dialogs.thief.show = true;
                    } else if (card.name === "dont_touch") {
                        this.dialogs.dont_touch.card_index = index;
                        this.dialogs.dont_touch.show = true;
                    } else if (card.name === "collapse") {
                        this.dialogs.collapse.card_index = index;
                        this.dialogs.collapse.show = true;
                    } else if (card.name === "enlighten") {
                        this.dialogs.enlighten.card_index = index;
                        this.dialogs.enlighten.show = true;
                    } else if (card.name === "inspection") {
                        this.dialogs.inspection.card_index = index;
                        this.dialogs.inspection.show = true;
                    } else if (card.name === "exchange_hats") {
                        this.dialogs.exchange_hats.card_index = index;
                        this.dialogs.exchange_hats.show = true;
                    } else if (card.name === "exchange_hands") {
                        this.dialogs.exchange_hands.card_index = index;
                        this.dialogs.exchange_hands.show = true;
                    } else {
                        console.warn(this.tag+" unknown action card: "+card.name);
                    }
                }
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
            onClickViewCard() {
                console.log(this.tag+" clicked view card button");
                // Find & save selected card's index
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        this.dialogs.view_card.index = i;
                        break;
                    }
                }
                // Show dialog
                this.dialogs.view_card.show = true;
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
            // View card dialog
            onClickCancelViewCard() {
                console.log(this.tag+" clicked cancel view card");
                // Hide the dialog
                this.dialogs.view_card.show = false;
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
                // Compose API request payload
                let data = { 
                    indices: this.dialogs.fold_cards_unblock.indices,
                    tool: this.dialogs.fold_cards_unblock.tool,
                };
                // Make API request
                this.sendPerformActionRequest("fold_cards_unblock", data)
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Remove cards from our hand
                        for (let i = 0; i < this.dialogs.fold_cards_unblock.indices.length; i++) {
                            this.mutableHand.splice(this.dialogs.fold_cards_unblock.indices[i], 1);
                        }
                        // Add new cards to hand if we received one
                        if (response.data.new_card) this.mutableHand.push({ card: response.data.new_card, selected: false });
                        // Unblock the mutablePlayer's selected tool 
                        if (data.tool === "pickaxe") {
                            this.mutablePlayer.pickaxe_available = true;
                        } else if (data.tool === "light") {
                            this.mutablePlayer.light_available = true;
                        } else if (data.tool === "cart") {
                            this.mutablePlayer.cart_available = true;
                        }
                        // Unblock mutablePlayers entry's selected tool
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === this.mutablePlayer.id) {
                                if (data.tool === "pickaxe") {
                                    this.mutablePlayers[i].pickaxe_available = true;
                                } else if (data.tool === "light") {
                                    this.mutablePlayers[i].light_available = true;
                                } else if (data.tool === "cart") {
                                    this.mutablePlayers[i].cart_available = true;
                                }
                                break;
                            }
                        }
                        // Stop loading
                        this.dialogs.fold_cards_unblock.loading = false;
                        // Hide & reset dialog
                        this.dialogs.fold_cards_unblock.show = false;
                        this.dialogs.fold_cards_unblock.tool = null;
                    }.bind(this))
                    // // Request failed
                    // .catch(function(response) {
                    //     console.warn(this.tag+" request failed: ", response.data);
                    //     // Stop loading
                    //     this.dialogs.fold_cards_unblock.loading = false;
                    // }.bind(this));
            },
            // Sabotage dialog
            onClickCancelSabotage() {
                console.log(this.tag+" clicked cancel sabotage dialog button");
                this.dialogs.sabotage.show = false;
            },
            onClickConfirmSabotage() {
                console.log(this.tag+" clicked submit sabotage dialog button");
                // Start loading
                this.dialogs.sabotage.loading = true;
                // Compose API request payload
                let data = {
                    index: this.dialogs.sabotage.card_index,
                    player_id: this.dialogs.sabotage.player_id,
                };
                // Grab the card we're about to play
                let card = this.mutableHand[this.dialogs.sabotage.card_index].card;
                // Determine the tool we're about to disable
                if (card.name === "sabotage_pickaxe_cart" || card.name === "sabotage_pickaxe_light" || card.name === "sabotage_light_cart") {
                    data.tool = this.dialogs.sabotage.tool;
                } else if (card.name === "sabotage_pickaxe") {
                    data.tool = "pickaxe";
                } else if (card.name === "sabotage_light") {
                    data.tool = "light";
                } else if (card.name === "sabotage_cart") {
                    data.tool = "cart";
                }
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded", response.data);
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.sabotage.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card, 
                            selected: false
                        });
                        // Update target player's tool status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                if (data.tool === "pickaxe") {
                                    this.mutablePlayers[i].pickaxe_available = false;
                                } else if (data.tool === "light") {
                                    this.mutablePlayers[i].light_available = false;
                                } else if (data.tool === "cart") {
                                    this.mutablePlayers[i].cart_available = false;
                                }
                                break;
                            }
                        }
                        // Stop loading
                        this.dialogs.sabotage.loading = false;
                        // Hide dialog
                        this.dialogs.sabotage.show = false;
                        // Reset dialog 
                        this.dialogs.sabotage.player_id = null;
                        this.dialogs.sabotage.tool = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed", response.data);
                        // Stop loading
                        this.dialogs.sabotage.loading = false;
                    }.bind(this));
            },
            // Recover dialog
            onClickCancelRecover() {
                console.log(this.tag+" clicked cancel recover dialog button");
                this.dialogs.recover.show = false;
            },
            onClickConfirmRecover() {
                console.log(this.tag+" clicked submit recover dialog button");
                // Start loading
                this.dialogs.recover.loading = true;
                // Compose API request payload
                let data = {
                    index: this.dialogs.recover.card_index,
                    player_id: this.dialogs.recover.player_id,
                };
                // Grab the card we want to play
                let card = this.mutableHand[this.dialogs.recover.card_index].card;
                // Determine the tool we want to recover
                if (card.name === "recover_pickaxe_cart" || card.name === "recover_pickaxe_light" || card.name === "recover_light_cart") {
                    data.tool = this.dialogs.recover.tool;
                } else if (card.name === "recover_pickaxe") {
                    data.tool = "pickaxe";
                } else if (card.name === "recover_light") {
                    data.tool = "light";
                } else if (card.name === "recover_cart") {
                    data.tool = "cart";
                }
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded", response.data);
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.recover.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false
                        });
                        // Update target player's tool status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                if (data.tool === "pickaxe") {
                                    this.mutablePlayers[i].pickaxe_available = true;
                                } else if (data.tool === "light") {
                                    this.mutablePlayers[i].light_available = true;
                                } else if (data.tool === "cart") {
                                    this.mutablePlayers[i].cart_available = true;
                                }
                                break;
                            }
                        }
                        // Update mutable player's tool status (if we targeted ourself)
                        if (this.mutablePlayer.id === data.player_id) {
                            if (data.tool === "pickaxe") {
                                this.mutablePlayer.pickaxe_available = true;
                            } else if (data.tool === "light") {
                                this.mutablePlayer.light_available = true;
                            } else if (data.tool === "cart") {
                                this.mutablePlayer.cart_available = true;
                            }
                        }
                        // Stop loading
                        this.dialogs.recover.loading = false;
                        // Hide dialog
                        this.dialogs.recover.show = false;
                        // Reset dialog 
                        this.dialogs.recover.player_id = null;
                        this.dialogs.recover.tool = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed", response.data);
                        // Stop loading
                        this.dialogs.recover.loading = false;
                    }.bind(this));
            },
            // Imprison dialog
            onClickCancelImprison() {
                console.log(this.tag+" clicked cancel imprison player button");
                // Hide dialog
                this.dialogs.imprison.show = false;
            },
            onClickConfirmImprison() {
                console.log(this.tag+" clicked confirm imprison player button");
                // Start loading
                this.dialogs.imprison.loading = true;
                // Compose API request data
                let data = {
                    index: this.dialogs.imprison.card_index,
                    player_id: this.dialogs.imprison.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.imprison.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false,
                        });
                        // Update player's status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                this.mutablePlayers[i].in_jail = true;
                                break;
                            }
                        }
                        // Stop loading
                        this.dialogs.imprison.loading = false;
                        // Hide dialog
                        this.dialogs.imprison.show = false;
                        // Reset dialog
                        this.dialogs.imprison.player_id = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.imprison.loading = false;
                    }.bind(this));
            },
            // Free dialog
            onClickCancelFree() {
                console.log(this.tag+" clicked cancel free player button");
                this.dialogs.free.show = false;
            },
            onClickConfirmFree() {
                console.log(this.tag+" clicked confirm free player button");
                // Start loading
                this.dialogs.free.loading = true;
                // Compose API request data
                let data = {
                    index: this.dialogs.free.card_index,
                    player_id: this.dialogs.free.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.free.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false,
                        });
                        // Update player's status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                this.mutablePlayers[i].in_jail = false;
                                break;
                            }
                        }
                        // Update mutable player's tool status (if we targeted ourself)
                        if (this.mutablePlayer.id === data.player_id) {
                            this.mutablePlayer.in_jail = false;
                        }
                        // Stop loading
                        this.dialogs.free.loading = false;
                        // Hide dialog
                        this.dialogs.free.show = false;
                        // Reset dialog
                        this.dialogs.free.player_id = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.free.loading = false;
                    }.bind(this));
            },
            // Thief dialog
            onClickCancelThief() {
                console.log(this.tag+" clicked cancel thief button");
                this.dialogs.thief.show = false;
            },
            onClickConfirmThief() {
                console.log(this.tag+" clicked confirm thief button");
                // Start loading
                this.dialogs.thief.loading = true;
                // Compose API request data
                let data = {
                    index: this.dialogs.thief.card_index,
                    player_id: this.dialogs.thief.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.thief.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false,
                        });
                        // Update player's status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === this.mutablePlayer.id) {
                                this.mutablePlayers[i].thief_activated = true;
                                break;
                            }
                        }
                        // Update mutable player's tool status (if we targeted ourself)
                        this.mutablePlayer.thief_activated = true;
                        // Stop loading
                        this.dialogs.thief.loading = false;
                        // Hide dialog
                        this.dialogs.thief.show = false;
                        // Reset dialog
                        this.dialogs.thief.player_id = null;
                    }.bind(this))
                    // Request failed
                    // .catch(function(response) {
                    //     console.log(this.tag+" request failed: ", response.data);
                    //     // Stop loading
                    //     this.dialogs.thief.loading = false;
                    // }.bind(this));
            },
            // Dont touch dialog
            onClickCancelDontTouch() {
                console.log(this.tag+" clicked cancel dont touch button");
                this.dialogs.dont_touch.show = false;
            },
            onClickConfirmDontTouch() {
                console.log(this.tag+" clicked confirm dont touch button");
                // Start loading
                this.dialogs.dont_touch.loading = true;
                // Compose API request data
                let data = {
                    index: this.dialogs.dont_touch.card_index,
                    player_id: this.dialogs.dont_touch.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.dont_touch.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({
                            card: response.data.new_card,
                            selected: false,
                        });
                        // Update player's status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                this.mutablePlayers[i].thief_activated = false;
                                break;
                            }
                        }
                        // Stop loading
                        this.dialogs.dont_touch.loading = false;
                        // Hide dialog
                        this.dialogs.dont_touch.show = false;
                        // Reset dialog
                        this.dialogs.dont_touch.player_id = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.dont_touch.loading = false;
                    }.bind(this));
            },
            // Enlighten dialog
            onClickCancelEnlighten() {
                console.log(this.tag+" clicked cancel enlighten button");
                this.dialogs.enlighten.show = false;
            },
            onClickConfirmEnlighten() {
                console.log(this.tag+" clicked confirm enlighten button");
                // Hide dialog
                this.dialogs.enlighten.show = false;
                // Transfer index of the card we're playing
                this.dialogs.confirm_enlighten.card_index = this.dialogs.enlighten.card_index;
                // Enable 'select gold location' mode
                this.modes.select_gold_location = true;
            },
            // Confirm enlighten dialog
            onClickCancelConfirmEnlighten() {
                console.log(this.tag+" clicked cancel confirm enlighten button");
                this.dialogs.confirm_enlighten.show = false;
            },
            onClickRetryConfirmEnlighten() {
                console.log(this.tag+" clicked retry confirm enlighten button");
                this.dialogs.confirm_enlighten.show = false;
                this.modes.select_gold_location = true;
            },
            onClickConfirmConfirmEnlighten() {
                console.log(this.tag+" clicked confirm confirm enlighten button");
                // Compose API request payload
                let data = {
                    index: this.dialogs.confirm_enlighten.card_index,
                    gold_location: this.dialogs.confirm_enlighten.gold_location,
                };
                // Send API requst
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.confirm_enlighten.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({ card: response.data.new_card, selected: false });
                        // Stop loading
                        this.dialogs.confirm_enlighten.loading = false;
                        // Close dialog
                        this.dialogs.confirm_enlighten.show = false;
                        // Open up gold reveal dialog
                        this.dialogs.reveal_gold_location.gold_location = this.dialogs.confirm_enlighten.gold_location;
                        this.dialogs.reveal_gold_location.contains_gold = response.data.contains_gold;
                        this.dialogs.reveal_gold_location.show = true;
                        // Reset dialog
                        this.dialogs.confirm_enlighten.gold_location = null;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.confirm_enlighten.loading = false;
                    }.bind(this));
            },
            // Inspection dialog
            onClickCancelInspection() {
                console.log(this.tag+" clicked cancel inspection button");
                // Hide dialog
                this.dialogs.inspection.show = false;
            },
            onClickConfirmInspection() {
                console.log(this.tag+" clicked confirm inspection button");
                // Start loading
                this.dialogs.inspection.loading = true;
                // Compose API request payload
                let data = {
                    index: this.dialogs.inspection.card_index,
                    player_id: this.dialogs.inspection.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    // Request succeeded
                    .then(function(response) {
                        console.log(this.tag+" request succeeded: ", response.data);
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.inspection.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({ card: response.data.new_card, selected: false });
                        // Populate the reveal role dialog
                        this.dialogs.reveal_role.player_id = this.dialogs.inspection.player_id;
                        this.dialogs.reveal_role.role = response.data.role;
                        // Hide inspection dialog
                        this.dialogs.inspection.loading = false;
                        this.dialogs.inspection.show = false;
                        // Show reveal role dialog
                        this.dialogs.reveal_role.show = true;
                    }.bind(this))
                    // Request failed
                    .catch(function(response) {
                        console.log(this.tag+" request failed: ", response.data);
                        // Stop loading
                        this.dialogs.inspection.loading = false;
                    }.bind(this));

            },
            // Reveal role dialog (result from inspection dialog)
            onClickCancelRevealRole() {
                console.log(this.tag+" clicked cancel reveal role button");
                // Hide dialog
                this.dialogs.reveal_role.show = false;
            },
            // Exchange hands dialog
            onClickCancelExchangeHands() {
                console.log(this.tag+" clicked cancel exchange hands button");
                // Hide dialog
                this.dialogs.exchange_hands.show = false;
            },
            onClickConfirmExchangeHands() {
                console.log(this.tag+" clicked confirm exchange hands button");
                // Start loading
                this.dialogs.exchange_hands.loading = true;
                // Compose API request payload
                let data = {
                    index: this.dialogs.exchange_hands.card_index,
                    player_id: this.dialogs.exchange_hands.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    .then(function(response) {
                        console.log(this.tag+" request succeeded", response.data);
                        // Update player's hand
                        this.initializeHand(response.data.new_hand);
                        if (response.data.new_card) this.mutableHand.push({ card: response.data.new_card, selected: false });
                        // Stop loading
                        this.dialogs.exchange_hands.loading = false;
                        // Hide dialog
                        this.dialogs.exchange_hands.show = false;
                    }.bind(this))
                    .catch(function(response) {
                        console.log(this.tag+" request failed", data);
                        // Stop loading
                        this.dialogs.exchange_hands.loading = false;
                    }.bind(this));
            },
            // Exchange hats dialog
            onClickCancelExchangeHats() {
                console.log(this.tag+" clicked cancel exchange hats button");
                // Hide dialog
                this.dialogs.exchange_hats.show = false;
            },
            onClickConfirmExchangeHats() {
                console.log(this.tag+" clicked confirm exchange hats button");
                // Start loading
                this.dialogs.exchange_hats.loading = true;
                // Compose API request payload
                let data = {
                    index: this.dialogs.exchange_hats.card_index,
                    player_id: this.dialogs.exchange_hats.player_id,
                };
                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    .then(function(response) {
                        console.log(this.tag+" request succeeded", data);
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.exchange_hats.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push({ card: response.data.new_card, selected: false });
                        // Stop loading
                        this.dialogs.exchange_hats.loading = false;
                        // Hide dialog
                        this.dialogs.exchange_hats.show = false;
                    }.bind(this))
                    .catch(function(response) {
                        console.log(this.tag+" request failed", data);
                        // Stop loading
                        this.dialogs.exchange_hats.loading = false;
                    }.bind(this));
            },
            // All dialogs that require player / tool selection
            onClickSelectPlayer(dialog, player_id) {
                if (dialog === "sabotage") {
                    if (this.dialogs.sabotage.player_id === player_id) {
                        this.dialogs.sabotage.player_id = null;
                    } else {
                        this.dialogs.sabotage.player_id = player_id;
                    }
                } else if (dialog === "recover") {
                    if (this.dialogs.recover.player_id === player_id) {
                        this.dialogs.recover.player_id = null;
                    } else {
                        this.dialogs.recover.player_id = player_id;
                    }
                } else if (dialog === "thief") {
                    if (this.dialogs.thief.player_id === player_id) {
                        this.dialogs.thief.player_id = null;
                    } else {
                        this.dialogs.thief.player_id = player_id;
                    }
                } else if (dialog === "dont_touch") {
                    if (this.dialogs.dont_touch.player_id === player_id) {
                        this.dialogs.dont_touch.player_id = null;
                    } else {
                        this.dialogs.dont_touch.player_id = player_id;
                    }
                } else if (dialog === "imprison") {
                    if (this.dialogs.imprison.player_id === player_id) {
                        this.dialogs.imprison.player_id = null;
                    } else {
                        this.dialogs.imprison.player_id = player_id;
                    }
                } else if (dialog === "free") {
                    if (this.dialogs.free.player_id === player_id) {
                        this.dialogs.free.player_id = null;
                    } else {
                        this.dialogs.free.player_id = player_id;
                    }
                } else if (dialog === "inspection") {
                    if (this.dialogs.inspection.player_id === player_id) {
                        this.dialogs.inspection.player_id = null;
                    } else {
                        this.dialogs.inspection.player_id = player_id;
                    }
                } else if (dialog === "exchange_hats") {
                    if (this.dialogs.exchange_hats.player_id === player_id) {
                        this.dialogs.exchange_hats.player_id = null;
                    } else {
                        this.dialogs.exchange_hats.player_id = player_id;
                    }
                } else if (dialog === "exchange_hands") {
                    if (this.dialogs.exchange_hands.player_id === player_id) {
                        this.dialogs.exchange_hands.player_id = null;
                    } else {
                        this.dialogs.exchange_hands.player_id = player_id;
                    }
                }
            },
            onClickSelectTool(dialog, tool) {
                if (dialog === "sabotage") {
                    if (this.dialogs.sabotage.tool === tool) {
                        this.dialogs.sabotage.tool = null;
                    } else {
                        this.dialogs.sabotage.tool = tool;
                    }
                } else if (dialog === "recover") {
                    if (this.dialogs.recover.tool === tool) {
                        this.dialogs.recover.tool = null;
                    } else {
                        this.dialogs.recover.tool = tool;
                    }
                } else if (dialog === "fold_cards_unblock") {
                    if (this.dialogs.fold_cards_unblock.tool === tool) {
                        this.dialogs.fold_cards_unblock.tool = null;
                    } else {
                        this.dialogs.fold_cards_unblock.tool = tool;
                    }
                }
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
            getCardIdByName(name) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].name === name) {
                        return this.cards[i].id;
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
        #game-ui__mode-wrapper {
            left: 0;
            top: 25px;
            z-index: 10;
            width: 100%;
            display: flex;
            position: absolute;
            flex-direction: row;
            justify-content: center;
            #game-ui__mode {
                display: flex;
                color: #ffffff;
                border-radius: 3px;
                padding: 10px 15px;
                box-sizing: border-box;
                background-color: hsl(0, 0%, 10%);
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
    .select-player {
        width: 100%;
        .select-player__title {
            margin: 0 0 10px 0;
        }
        .select-player__list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            margin: 0 -15px -30px -15px;
            .select-player__list-item {
                flex: 0 0 50%;
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
                .player-option {
                    padding: 15px;
                    color: #000;
                    border-radius: 3px;
                    transition: all .3s;
                    box-sizing: border-box;
                    background-color: rgba(255, 255, 255, .25);
                    &:hover {
                        cursor: pointer;
                        background-color: rgba(255, 255, 255, .5);
                    }
                    &.selected {
                        background-color: rgba(255, 255, 255, 1);
                        &:hover {
                            background-color: rgba(255, 255, 255, 1);
                        }
                    }
                }
            }
        }
    }
    .select-tool {
        width: 100%;
        margin: 15px 0 0 0;
        .select-tool__title {
            margin: 0 0 10px 0;
        }
        .select-tool__list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            justify-content: center;
            margin: 0 -15px -30px -15px;
            .select-tool__list-item {
                flex: 0 0 33.33%;
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
                .tool-option {
                    padding: 15px;
                    color: #000;
                    border-radius: 3px;
                    transition: all .3s;
                    box-sizing: border-box;
                    text-transform: capitalize;
                    background-color: rgba(255, 255, 255, .25);
                    &:hover {
                        cursor: pointer;
                        background-color: rgba(255, 255, 255, .5);
                    }
                    &.selected {
                        background-color: rgba(255, 255, 255, 1);
                        &:hover {
                            background-color: rgba(255, 255, 255, 1);
                        }
                    }
                }
            }
        }
    }
    #reveal-gold-location {
        display: flex;
        flex-direction: column;
        justify-content: center;
        #reveal-gold-location__image {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px auto;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
        }
        #reveal-gold-location__text {
            text-align: center;
        }
    }
    #new-role {
        padding: 15px;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: hsl(0, 0%, 5%);
        #new-role__name {
            font-size: 1.1em;
            font-weight: bold;
            margin: -5px 0 5px 0;
        }
        #new-role__description {
            font-size: .9em;
        }
    }
    .text-box {
        padding: 15px;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: hsl(0, 0%, 5%);
    }
</style>