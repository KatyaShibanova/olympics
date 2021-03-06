export class Service {
    key = 'olympicsToken';
    url = 'http://localhost/olympics';
    constructor() {

        this.base_url = "http://localhost/olympics/controller.php?";
        this.token = sessionStorage.getItem(this.key);
    }

    get(url) {
        return fetch(url).then(response => {
            if (response.ok) {
                // если HTTP-статус в диапазоне 200-299
                // получаем тело ответа (см. про этот метод ниже)
                return response.json();
            } else {
                console.error("Ошибка HTTP: " + response.status);
            }
        });
    }

    post(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(data)
        }).then(response => {
            if (response.ok) {
                // если HTTP-статус в диапазоне 200-299
                // получаем тело ответа (см. про этот метод ниже)
                return response.json();
            } else {
                console.error("Ошибка HTTP: " + response.status);
            }
        });
    }

    getPetitions() {
        const url = `${this.base_url}key=get-petitions&token=${this.token}`;
        return this.get(url);
    }

    getPrepodPetitions() {
        const url = `${this.base_url}key=get-prepod-petitions&token=${this.token}`;
        return this.get(url);
    }

    getPrepodWorks() {
        const url = `${this.base_url}key=get-prepod-works&token=${this.token}`;
        return this.get(url);
    }

    getWorkAnswers(id) {
        const url = `${this.base_url}key=get-work-answers&token=${this.token}&workID=${id}`;
        return this.get(url);
    }

    getPetitionDecision(id) {
        const url = `${this.base_url}key=get-petiton-decision&token=${this.token}&petitionID=${id}`;
        return this.get(url);
    }
    getWorks(id) {
        const url = `${this.base_url}key=get-works&token=${this.token}&userID=${id}`;
        return this.get(url);
    }

    logIn(email, password) {
        const url = `${this.base_url}key=log-in`;
        return this.post(url, { email, password });
    }
    saveAnswer(answer) {
        const url = `${this.base_url}key=save-answer&token=${this.token}`;
        return this.post(url, answer);
    }
    getTasks() {
        const url = `${this.base_url}key=get-tasks&token=${this.token}`;
        return this.get(url);
    }

    

    getScore(score) {
        const url = `${this.base_url}key=get-score&token=${this.token}`;
        return this.get(url, score);
    }
    createPetition(petition) {
        const url = `${this.base_url}key=create-petition&token=${this.token}`;
        return this.post(url, petition);
    }
    setPetition(petition) {
        const url = `${this.base_url}key=set-petition&token=${this.token}`;
        return this.post(url, petition);
    }
    setScore(score) {
        const url = `${this.base_url}key=set-score&token=${this.token}`;
        return this.post(url, score);
    }
}

export default Service;