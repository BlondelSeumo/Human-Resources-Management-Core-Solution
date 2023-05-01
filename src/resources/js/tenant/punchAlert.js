import {axiosGet, urlGenerator} from "../common/Helper/AxiosHelper";
import moment from "moment-timezone";

if (window.user) {
    let check = 1;
    let checkAlert = () => {
        axiosGet(`punch-in-alert?timeZone=${moment.tz.guess()}`).then(({data}) => {
            if (data.showAlert && check !== 1) {
                if (data.alert_area?.includes('web')){
                    swal(data.message);
                }
                if (data.alert_area?.includes('system')){
                    notifySystem(data.message);
                }
            }
            if (data.punchInAlert) {
                check++;
                setTimeout(checkAlert, Number(data.interval) * 1000);
            }
        }).catch(({response}) => {
            console.log(response.data.message);
        })
    }
    checkAlert();
}


function swal(message, colorCode = "#0d8cef", showConfirm = true) {
    Swal.fire({
        title: "Attention!",
        text: message,
        showCancelButton: true,
        confirmButtonColor: `${colorCode}`,
        showConfirmButton: showConfirm,
        confirmButtonText: 'Dashboard',
        imageWidth: 100,
        imageHeight: 70,
        imageAlt: "Warning"
    }).then(function (response) {
        if (!response.dismiss) {
            window.location = urlGenerator("/dashboard");
        }
    });
}

function notifySystem(message){
    Notification.requestPermission().then((permission)=>{
        if (permission === 'granted'){
            const notification = new Notification(message, {
                icon: urlGenerator(window.settings.tenant_icon)
            });

            notification.addEventListener("click", () => {
                window.focus();
                window.location = urlGenerator("/dashboard");
            })
        }
    })
}