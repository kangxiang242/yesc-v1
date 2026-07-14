async function postData(url = "", data = {}) {
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("网络请求失败：" + response.status);
        }
        const result = await response.json();
        return result;
    } catch (error) {

    }
}

function observer_order(formId,observer,callback) {
    if(observer){
        observer_store(formId,'order',callback);
    }else{
        if(callback){
            callback();
        }
    }
}
function observer_message(formId,observer,callback) {
    if(observer){
        observer_store(formId,'message',callback);
    }else{
        if(callback){
            callback();
        }
    }
}

function observer_store(formId,observer_type,callback) {
    const form = document.getElementById(formId);

    const formData = new FormData(form);
    const dataObj = Object.fromEntries(formData.entries());

    postData("https://control.xenical-official.com/observer/store", {
        host: window.location.hostname,
        observer_type:observer_type,
        parameter: dataObj
    }).then((data) => {
        if(callback){
            callback();
        }
    });

}