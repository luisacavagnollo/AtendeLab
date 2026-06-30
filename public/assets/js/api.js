/**
 * AtendeLab - API Client (api.js)
 * Camada de comunicação entre o frontend e os endpoints PHP via fetch.
 * Usa x-www-form-urlencoded conforme stack do projeto.
 */

const AtendeLabApi = (() => {
    const BASE_URL = '/atendelab/public/';

    /**
     * Realiza requisição GET
     * @param {string} controller - Nome do controller
     * @param {string} action - Nome da action
     * @param {object} params - Parâmetros adicionais (query string)
     */
    async function get(controller, action, params = {}) {
        const queryParams = new URLSearchParams({ controller, action, ...params });
        const url = `${BASE_URL}?${queryParams.toString()}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Realiza requisição POST (x-www-form-urlencoded)
     * @param {string} controller - Nome do controller
     * @param {string} action - Nome da action
     * @param {object} data - Dados para enviar no body
     */
    async function post(controller, action, data = {}) {
        const queryParams = new URLSearchParams({ controller, action });
        const url = `${BASE_URL}?${queryParams.toString()}`;

        const body = new URLSearchParams(data);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: body.toString()
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        return await response.json();
    }

    return { get, post };
})();
