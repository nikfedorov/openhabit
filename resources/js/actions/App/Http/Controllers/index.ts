import Auth from './Auth'
import Telegram from './Telegram'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    Telegram: Object.assign(Telegram, Telegram),
}

export default Controllers