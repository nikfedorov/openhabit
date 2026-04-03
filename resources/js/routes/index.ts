import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see routes/web.php:16
* @route '/telegram-miniapp'
*/
export const telegramMiniapp = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: telegramMiniapp.url(options),
    method: 'get',
})

telegramMiniapp.definition = {
    methods: ["get","head"],
    url: '/telegram-miniapp',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:16
* @route '/telegram-miniapp'
*/
telegramMiniapp.url = (options?: RouteQueryOptions) => {
    return telegramMiniapp.definition.url + queryParams(options)
}

/**
* @see routes/web.php:16
* @route '/telegram-miniapp'
*/
telegramMiniapp.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: telegramMiniapp.url(options),
    method: 'get',
})

/**
* @see routes/web.php:16
* @route '/telegram-miniapp'
*/
telegramMiniapp.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: telegramMiniapp.url(options),
    method: 'head',
})

/**
* @see routes/web.php:23
* @route '/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:23
* @route '/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:23
* @route '/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:23
* @route '/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

