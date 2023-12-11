declare global {
    const APP_ENV: string;

    interface Window {
        BASE_URI: string;
        BASE_URL: string;

        Utils: any;
        translateStrings: any;

        // Third party stuff
        helperBsTooltips: any;
        helperBsPopovers: any;

        $: any;
        jQuery: any;
    }
}

export {};
