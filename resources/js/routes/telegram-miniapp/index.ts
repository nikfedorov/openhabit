import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
export const auth = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: auth.url(options),
    method: 'post',
})

auth.definition = {
    methods: ["post"],
    url: '/telegram-miniapp/auth',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
auth.url = (options?: RouteQueryOptions) => {
    return auth.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
auth.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: auth.url(options),
    method: 'post',
})

const telegramMiniapp = {
    auth: Object.assign(auth, auth),
}

export default telegramMiniapp