<template>
    <div id="change-password-form" class="elevation-1">

        <!-- Current password -->
        <div class="form-field">
            <v-text-field
                type="password"
                name="password"
                :label="passwordText"
                v-model="form.password"
                :errors="hasErrors('new_password')"
                :error-messages="getErrors('new_password')">
            </v-text-field>
        </div>

        <!-- New password -->
        <div class="form-field">
            <v-text-field
                type="password"
                name="new_password"
                :label="newPasswordText"
                v-model="form.new_password"
                :errors="hasErrors('new_password')"
                :error-mesages="getErrors('new_password')">
            </v-text-field>
        </div>

        <!-- Confirm new password -->
        <div class="form-field">
            <v-text-field
                type="password"
                name="new_password_confirmation"
                :label="confirmNewPasswordText"
                v-model="form.new_password_confirmation"
                :errors="hasErrors('new_password_confirmation')"
                :error-messages="getErrors('new_password_confirmation')">
            </v-text-field>
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
                <v-btn color="primary" type="submit">
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
            "errors",
            "passwordText",
            "newPasswordText",
            "confirmNewPasswordText",
            "cancelText",
            "cancelHref",
            "submitText",
        ],
        data: () => ({
            tag: "[change-password-form]",
            form: {
                password: "",
                new_password: "",
                confirm_new_password: "",
            }
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" errors: ", this.errors);
                console.log(this.tag+" password text: ", this.passwordText);
                console.log(this.tag+" new password text: ", this.newPasswordText);
                console.log(this.tag+" confirm new password text: ", this.confirmNewPasswordText);
                console.log(this.tag+" cancel text: ", this.cancelText);
                console.log(this.tag+" cancel href: ", this.cancelHref);
                console.log(this.tag+" submit text: ", this.submitText);
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
    #change-password-form {
        padding: 25px;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #fff;
    }
</style>