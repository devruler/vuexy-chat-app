<template>
    <div class="iframe-container">
        <meta charset="utf-8" />
        <link
            type="text/css"
            rel="stylesheet"
            href="https://source.zoom.us/1.9.1/css/bootstrap.css"
        />
        <link
            type="text/css"
            rel="stylesheet"
            href="https://source.zoom.us/1.9.1/css/react-select.css"
        />

        <meta name="format-detection" content="telephone=no" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="origin-trial" content="">
    </div>
</template>
<script>
import { ZoomMtg } from "@zoomus/websdk";

// import "@zoomus/websdk/dist/css/bootstrap.css"
// import "@zoomus/websdk/dist/css/react-select.css"

console.log(ZoomMtg);
console.log("checkSystemRequirements");
console.log(JSON.stringify(ZoomMtg.checkSystemRequirements()));

// CDN version default
ZoomMtg.setZoomJSLib("https://source.zoom.us/1.9.1/lib", "/av");
ZoomMtg.preLoadWasm();
ZoomMtg.prepareJssdk();

const API_KEY = "QvxY3s-PQQuKBJsxUn01ug";
const API_SECRET = "F2WZB9B2ERmwE0vIyCmqINCyhT4wi9cGgjAl";

export default {
    name: "ZoomFrame",
    data: function () {
        return {
            src: "",
            meetConfig: {},
            signature: {},
        };
    },
    props: {
        nickname: String,
        meetingId: Number,
        password: String,
    },
    created: function () {
        // Meeting config object
        this.meetConfig = {
            apiKey: API_KEY,
            apiSecret: API_SECRET,
            meetingNumber: this.meetingId,
            userName: this.nickname,
            passWord: this.password,
            leaveUrl: "http://" + window.location.hostname,
            role: 1,
        };
        // Generate Signature function
        this.signature = ZoomMtg.generateSignature({
            meetingNumber: this.meetConfig.meetingNumber,
            apiKey: this.meetConfig.apiKey,
            apiSecret: this.meetConfig.apiSecret,
            role: this.meetConfig.role,
            success: function (res) {
                // eslint-disable-next-line
                console.log("success signature: " + res.result);
            },
        });
        // join function
        ZoomMtg.init({
            leaveUrl: "http://" + window.location.hostname,
            isSupportAV: true,
            success: () => {
                console.log("success");
                ZoomMtg.join({
                    meetingNumber: this.meetConfig.meetingNumber,
                    userName: this.meetConfig.userName,
                    signature: this.signature,
                    apiKey: this.meetConfig.apiKey,
                    // userEmail: "email@gmail.com",
                    passWord: this.meetConfig.passWord,
                    success: function (res) {
                        // eslint-disable-next-line
                        console.log("join meeting success");
                    },
                    error: function (res) {
                        // eslint-disable-next-line
                        console.log("error join meeting:", res);
                    },
                });
            },
            error: function (res) {
                // eslint-disable-next-line
                console.log("error");
                console.log(res);
            },
        });
    },
    mounted: function () {},
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
.btn-default {
    color: #333;
    background-color: unset!important;
    border-color: rgba(204, 204, 204, 0.541)!important;
}
.chat-receiver-list__receiver{
    background-color: #0098e0!important;
    font-size: small;
}
.btn-group .chat-receiver-list__receiver {
    padding: 2px 5px!important;
}
.audio-option-menu__pop-menu{
    bottom: 100%!important;
    left: 100px!important;
    top: auto!important;
}
</style>
