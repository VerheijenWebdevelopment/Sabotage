<template>
    <div id="game-board">
        <div id="game-board__inner">

            <!-- Board -->
            <div id="board" 
                :class="{ 'zoom-out': zoom === 0, 'zoom-in': zoom === 2 }"
                :style="{ top: viewportY+'px', left: viewportX+'px' }" 
                @mousedown="onMouseDown" 
                @mousemove="onMouseMove"
                @mouseup="onMouseUp">
                <div class="board-row" v-for="(row, ri) in value" :key="ri">
                    <div class="board-cell" v-for="(card, ci) in row" :key="ci" @click="onClickTile(ri, ci)">
                        <div class="coordinates">{{ ci+","+ri }}</div>
                        <div class="card" v-if="card !== null">
                            <div class="card-image" :class="{ inverted: card.inverted }" :style="{ backgroundImage: 'url('+getCardImageById(card.card_id)+')' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Controls -->
            <div id="board-controls">
                <!-- Zoom out -->
                <v-btn dark small class="icon-only" :disabled="zoomOutDisabled" @click="onClickZoomOut">
                    <i class="fas fa-minus"></i>
                </v-btn>
                <!-- Zoom in -->
                <v-btn dark small class="icon-only" :disabled="zoomInDisabled" @click="onClickZoomIn">
                    <i class="fas fa-plus"></i>
                </v-btn>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props: [
            "value",
            "cards",
        ],
        data: () => ({
            tag: "[game-board]",
            dragging: false,
            startX: null,
            startY: null,
            viewportX: 0,
            viewportY: 0,
            x: 0,
            y: 0,
            zoom: 1,
        }),
        computed: {
            zoomOutDisabled() {
                return this.zoom === 0;
            },
            zoomInDisabled() {
                return this.zoom === 2;
            },
        },
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" value: ", this.value);
                console.log(this.tag+" cards: ", this.cards);
                this.centerBoard();
            },
            onMouseDown(e) {
                // console.log("mousedown", e);
                this.dragging = true;
                this.startX = e.clientX;
                this.startY = e.clientY;
            },
            onMouseMove(e) {
                if (this.dragging) {
                    // console.log("mouse moving", e);
                    let dX = e.clientX - this.startX;
                    let dY = e.clientY - this.startY;
                    this.viewportX = this.viewportX + dX;
                    this.viewportY = this.viewportY + dY;
                    this.startX = e.clientX;
                    this.startY = e.clientY;
                }
            },
            onMouseUp(e) {
                // console.log("stopping drag");
                this.dragging = false;
            },
            getCardImageById(id) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].id === id) {
                        return this.cards[i].image_url;
                    }
                }
                return "";
            },
            getCardIdByName(name) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].name === name) {
                        return this.cards[i].id;
                    }
                }
                return 0;
            },
            centerBoard() {

                // Grab the ID of the start card
                let startCardId = this.getCardIdByName("start");

                // Grab the coordinates of the start card on the board
                let startCoordinates = null;
                for (let i = 0; i < this.value.length; i++) {
                    for (let j = 0; j < this.value[i].length; j++) {
                        if (this.value[i][j] !== null && this.value[i][j].card_id === startCardId) {
                            startCoordinates = { rowIndex: i, columnIndex: j };
                            break;
                        }
                    }
                }

                // Determine the coordinates of the center card (relative to start & 2nd gold location card)
                let centerCoordinates = startCoordinates;
                centerCoordinates.columnIndex += 4;

                // Calculate the X & Y coordinate of the center card on the board
                let centerX = 130 * centerCoordinates.columnIndex + (130/2);
                let centerY = 200 * centerCoordinates.rowIndex + (200/2);

                // Grab the dimensions of the viewport
                let viewportWidth = $("#game-board__inner").width();
                let viewportHeight = $("#game-board__inner").height();

                // Calculate the center of the viewport
                let viewportCenterX = viewportWidth / 2;
                let viewportCenterY = viewportHeight / 2;

                // Calculate the difference between them; which in turn is the desired position of the board within the viewport
                let differenceX = viewportCenterX - centerX;
                let differenceY = viewportCenterY - centerY;

                // Center the board
                this.viewportX = differenceX;
                this.viewportY = differenceY;

            },
            onClickTile(rowIndex, columnIndex) {
                this.$emit("clicked-tile", {
                    rowIndex: rowIndex, 
                    columnIndex: columnIndex
                });
            },
            onClickZoomIn() {
                this.zoom += 1;
                this.centerBoard();
            },
            onClickZoomOut() {
                this.zoom -= 1;
                this.centerBoard();
            },
        },
        mounted() {
            this.initialize();
        },
    }
</script>

<style lang="scss">
    #game-board {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        #game-board__inner {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
            #board {
                top: 0;
                left: 0;
                width: 100%;
                position: absolute;
                &.zoom-out {
                    .board-row {
                        .board-cell {
                            height: 100px;
                            flex: 0 0 65px;
                            .card {
                                .card-image {
                                    width: 65px;
                                    height: 100px;
                                }
                            }
                        }
                    }
                }
                &.zoom-in {
                    .board-row {
                        .board-cell {
                            height: 400px;
                            flex: 0 0 260px;
                            .card {
                                .card-image {
                                    width: 260px;
                                    height: 400px;
                                }
                            }
                        }
                    }
                }
                .board-row {
                    display: flex;
                    flex-direction: row;
                    border-left: 1px dashed rgba(255, 255, 255, 0.1);
                    border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
                    &:first-child {
                        border-top: 1px dashed rgba(255, 255, 255, 0.1);
                    }
                    .board-cell {
                        height: 200px;
                        flex: 0 0 130px;
                        position: relative;
                        overflow: hidden;
                        box-sizing: border-box;
                        border-right: 1px dashed rgba(255, 255, 255, 0.1);
                        .coordinates {
                            top: 15px;
                            left: 15px;
                            z-index: 5;
                            font-size: .8em;
                            position: absolute;
                            color: rgba(255, 255, 255, .15);
                        }
                        .card {
                            top: 0;
                            left: 0;
                            position: absolute;
                            .card-image {
                                width: 130px;
                                height: 200px;
                                border-radius: 3px;
                                background-size: contain;
                                background-repeat: no-repeat;
                                background-position: center center;
                                &.inverted {
                                    transform: rotate(180deg);
                                }
                            }
                        }
                    }
                }
            }
            #board-controls {
                right: 25px;
                bottom: 300px;
                position: absolute;
                .v-btn {
                    padding: 0 9px;
                    margin: 0 0 0 7px;
                }
            }
        }   
    }
</style>