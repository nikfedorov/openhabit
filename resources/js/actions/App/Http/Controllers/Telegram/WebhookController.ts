import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
const WebhookController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: WebhookController.url(options),
    method: 'post',
})

WebhookController.definition = {
    methods: ["post"],
    url: '/telegram/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
WebhookController.url = (options?: RouteQueryOptions) => {
    return WebhookController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
WebhookController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: WebhookController.url(options),
    method: 'post',
})

export default WebhookController