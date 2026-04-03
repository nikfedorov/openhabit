import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/telegram/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Telegram\WebhookController::__invoke
* @see app/Http/Controllers/Telegram/WebhookController.php:12
* @route '/telegram/webhook'
*/
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

const telegram = {
    webhook: Object.assign(webhook, webhook),
}

export default telegram