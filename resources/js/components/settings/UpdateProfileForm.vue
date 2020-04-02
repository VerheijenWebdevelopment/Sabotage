<template>
    <div id="update-profile-form" class="elevation-1">

        <!-- Username -->
        <div class="form-field">
            <v-text-field
                name="username"
                :label="usernameText"
                v-model="form.username"
                :errors="hasErrors('username')"
                :error-messages="getErrors('username')">
            </v-text-field>
        </div>

        <!-- Name -->
        <div class="form-field">
            <v-text-field
                name="name"
                :label="nameText"
                v-model="form.name"
                :errors="hasErrors('name')"
                :error-messages="getErrors('name')">
            </v-text-field>
        </div>

        <!-- Email -->
        <div class="form-field">
            <v-text-field
                name="email"
                :label="emailText"
                v-model="form.email"
                :errors="hasErrors('email')"
                :error-messages="getErrors('email')">
            </v-text-field>
        </div>
        
        <!-- Avatar -->
        <div class="image-field">
            <div class="image-field__label">{{ avatarText }}</div>
            <div class="image-field__image-wrapper">
                <div class="image-field__image" :style="{ backgroundImage: 'url('+user.avatar_url+')' }"></diV>
            </div>
            <div class="image-field__input">
                <input type="file" name="avatar">
            </div>
            <div class="image-field__errors" v-if="hasErrors('avatar')">
                <div class="image-field__error" v-for="(error, ei) in getErrors('avatar')" :key="ei">
                    {{ error }}
                </div>
            </div>
        </div>

        <!-- Form controls -->
        <div class="form-controls">
            <div class="form-controls__left">
                <v-btn text :href="cancelHref">
                    <i class="fas fa-arrow-left"></i>
                    {{ cancelText }}
                </v-btn>    
            </div>
            <div class="form-controls__right">
                <v-btn depressed color="primary" type="submit">
                    <i class="fas fa-save"></i>
                    {{ submitText }}
                </v-btn>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "user",
            "errors",
            "oldInput",
            "usernameText",
            "nameText",
            "emailText",
            "avatarText",
            "cancelText",
            "cancelHref",
            "submitText",
        ],
        data: () => ({
            tag: "[update-profile-form]",
            form: {
                name: "",
                email: "",
                username: "",
            }
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" user: ", this.user);
                console.log(this.tag+" errors: ", this.errors);
                console.log(this.tag+" old input: ", this.oldInput);
                console.log(this.tag+" username text: ", this.usernameText);
                console.log(this.tag+" name text: ", this.nameText);
                console.log(this.tag+" email text: ", this.emailText);
                console.log(this.tag+" avatar text: ", this.avatarText);
                console.log(this.tag+" cancel text: ", this.cancelText);
                console.log(this.tag+" cancel href: ", this.cancelHref);
                console.log(this.tag+" submit text: ", this.submitText);
                this.initializeData();
            },
            initializeData() {
                if (this.user !== undefined && this.user !== null) {
                    this.form.name = this.user.name;
                    this.form.email = this.user.email;
                    this.form.username = this.user.username;
                }
                if (this.oldInput !== undefined && this.oldInput !== null) {
                    if (this.oldInput.name !== null) this.form.name = this.oldInput.name;
                    if (this.oldInput.email !== null) this.form.email = this.oldInput.email;
                    if (this.oldInput.username !== null) this.form.username = this.oldInput.username;
                }
            },
            hasErrors(field) {
                if (this.errors !== undefined && this.errors !== null && this.errors.length > 0) {
                    if (this.errors[field] !==  undefined &&  this.errors[field] !== "") {
                        return true;
                    }
                }
                return false;
            },
            getErrors(field) {
                if (this.errors !== undefined && this.errors !== null && this.errors[field] !== undefined && this.errors[field] !== null) {
                    return this.errors[field];
                }
                return [];
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #update-profile-form {
        padding: 25px;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #ffffff;
        .image-field__image {
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-color: hsl(0, 0%, 95%);
        }
    }
</style>