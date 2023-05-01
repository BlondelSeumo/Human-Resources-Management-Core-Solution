<template>

    <app-pre-loader v-if="preloader"/>
    <div v-else>
        <app-note
            class="mb-primary"
            :title="$t('note')"
            :notes="$t('cron_job_setting_warning')"
        />
        <p>{{ this.$t('cron_job_setting_suggestion') }}</p>
        <a href="https://payday.gainhq.com/documentation/important-settings.html#scheduler-queue" target="_blank"
           class="btn btn-primary mb-primary">
            <app-icon name="alert-circle" class="size-18 mr-2"/>
            {{ this.$t('see_documentation') }}</a>

        <div class="mb-primary">
            <h4>1. For Cpanel service provider</h4>
            <p class="pb-2 border-bottom">Please copy the command below and insert into your hosted server's crontab.</p>
            <div class="row">
                <div class="col-lg-3 col-xl-3 col-md-12">
                    {{ $t('command_with_php_path') }}
                </div>
                <div class="col-lg-9 col-xl-9 col-md-12 d-inline-flex justify-content-between flex-wrap">
                    <p ref="cmd" id="with_php_cmd" v-on:focus="$event.target.select()" readonly>
                        <code>{{ settings.cpanel_command }}</code>
                    </p>
                    <button type="button"
                            class="btn btn-sm d-inline-flex width-100 height-30 align-items-center justify-content-center"
                            :class="isCopied ? 'btn-success' : 'btn-warning'"
                            @mouseleave="afterCopied('with_php_cmd')"
                            @click="copy('with_php_cmd')">
                        <span v-if="isCopied" :key="'check1'">
                            <app-icon name="check" class="size-18 mr-2"/> {{ $t('copied') }}
                        </span>
                        <span v-else :key="'copy1'">
                            <app-icon name="copy" class="size-18 mr-2"/> {{ $t('copy') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="mb-primary">
            <h4>2. For other service provider</h4>
            <p class="pb-2 border-bottom">If you are not using Cpanel and the php path of your hosted server is different from
                the Cpanel php path, then please identity your server's php path and add the path in front of the command below
                and then insert into your server's crontab.</p>
            <div class="row">
                <div class="col-lg-3 col-xl-3 col-md-12">
                    {{ $t('command_without_php_path') }}
                </div>
                <div class="col-lg-9 col-xl-9 col-md-12 d-inline-flex justify-content-between flex-wrap">
                    <p ref="cmd" id="without_php_cmd" v-on:focus="$event.target.select()" readonly>
                        <code>{{ settings.command }}</code>
                    </p>
                    <button type="button"
                            class="btn btn-sm d-inline-flex width-100 height-30 align-items-center justify-content-center"
                            :class="isCmdCopied ? 'btn-success' : 'btn-warning'"
                            @mouseleave="afterCopied('without_php_cmd')"
                            @click="copy('without_php_cmd')">
                        <span v-if="isCmdCopied" :key="'check2'">
                            <app-icon name="check" class="size-18 mr-2"/> {{ $t('copied') }}
                        </span>
                        <span v-else :key="'copy2'">
                            <app-icon name="copy" class="size-18 mr-2"/> {{ $t('copy') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="alert alert-warning text-dark">
            If you find any problem in running jobs please check your php.ini/php extension configuration.<br>
            Make sure there are no function that are called by the queue driver,
            such as, <kbd>proc_open</kbd>, <kbd>pcntl_alarm</kbd>, <kbd>pcntl_async_signals</kbd>,
            <kbd>pcntl_signal</kbd> in the <code>disable_functions</code>.<br>
            If there any you’ll need to remove/enable those functions. Or you can contact with your hosting service
            provider.
        </div>
    </div>

</template>

<script>


import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {CRON_JOB_SETTING} from "../../../../../common/Config/apiUrl";

export default {
    name: "CronJobSettings.vue",
    data() {
        return {
            settings: [],
            preloader: false,
            isCopied: false,
            isCmdCopied: false,
        }
    },
    methods: {
        getCronJobSettings() {
            this.preloader = true;
            axiosGet(CRON_JOB_SETTING).then(({data}) => {
                this.settings = data;
            }).finally(() => {
                this.preloader = false;
            })
        },
        copy(id) {
            let copyText = document.getElementById(id);
            let input = document.createElement("textarea");
            input.value = copyText.textContent;
            document.body.appendChild(input);
            input.select();
            document.execCommand("Copy");
            input.remove();
            id === 'with_php_cmd' ?  this.isCopied = true : this.isCmdCopied = true;
        },
        afterCopied(id) {
            setTimeout(() => {
                id === 'with_php_cmd' ?  this.isCopied = false : this.isCmdCopied = false;
            }, 1000)
        }
    },
    created() {
        this.getCronJobSettings();
    }

}
</script>

<style scoped>

</style>