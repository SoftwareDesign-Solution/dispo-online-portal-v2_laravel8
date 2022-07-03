import axios from "axios";

//console.log(window.APP_URL);

const instance = axios.create({
    baseURL: window.APP_URL + '/api',
    //baseURL: 'https://dispo.trendline.eu/api',
    //baseURL: 'http://dispov2.saas-apphosting.de/api',
    headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('jwt')}`
    }
});

export default instance;
