// axios
import axios from 'axios'

const baseURL = ''

export default axios.create({
    baseURL,
    // You can add your headers here

    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    }
})
