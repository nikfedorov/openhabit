import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
const TelegramMiniAppController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: TelegramMiniAppController.url(options),
    method: 'post',
})

TelegramMiniAppController.definition = {
    methods: ["post"],
    url: '/telegram-miniapp/auth',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
TelegramMiniAppController.url = (options?: RouteQueryOptions) => {
    return TelegramMiniAppController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\TelegramMiniAppController::__invoke
* @see app/Http/Controllers/Auth/TelegramMiniAppController.php:14
* @route '/telegram-miniapp/auth'
*/
TelegramMiniAppController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: TelegramMiniAppController.url(options),
    method: 'post',
})

export default TelegramMiniAppController