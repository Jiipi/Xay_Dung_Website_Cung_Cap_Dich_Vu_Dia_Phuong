import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/provider/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::index
* @see app/Http/Controllers/Provider/ProviderServiceController.php:27
* @route '/provider/services'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/provider/services/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::create
* @see app/Http/Controllers/Provider/ProviderServiceController.php:61
* @route '/provider/services/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::store
* @see app/Http/Controllers/Provider/ProviderServiceController.php:85
* @route '/provider/services'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/provider/services',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::store
* @see app/Http/Controllers/Provider/ProviderServiceController.php:85
* @route '/provider/services'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::store
* @see app/Http/Controllers/Provider/ProviderServiceController.php:85
* @route '/provider/services'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::store
* @see app/Http/Controllers/Provider/ProviderServiceController.php:85
* @route '/provider/services'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::store
* @see app/Http/Controllers/Provider/ProviderServiceController.php:85
* @route '/provider/services'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/provider/services/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::show
* @see app/Http/Controllers/Provider/ProviderServiceController.php:106
* @route '/provider/services/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
export const edit = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/provider/services/{id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
edit.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return edit.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
edit.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
edit.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
const editForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
editForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::edit
* @see app/Http/Controllers/Provider/ProviderServiceController.php:212
* @route '/provider/services/{id}/edit'
*/
editForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::update
* @see app/Http/Controllers/Provider/ProviderServiceController.php:259
* @route '/provider/services/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/provider/services/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::update
* @see app/Http/Controllers/Provider/ProviderServiceController.php:259
* @route '/provider/services/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::update
* @see app/Http/Controllers/Provider/ProviderServiceController.php:259
* @route '/provider/services/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::update
* @see app/Http/Controllers/Provider/ProviderServiceController.php:259
* @route '/provider/services/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::update
* @see app/Http/Controllers/Provider/ProviderServiceController.php:259
* @route '/provider/services/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::destroy
* @see app/Http/Controllers/Provider/ProviderServiceController.php:286
* @route '/provider/services/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/provider/services/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::destroy
* @see app/Http/Controllers/Provider/ProviderServiceController.php:286
* @route '/provider/services/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::destroy
* @see app/Http/Controllers/Provider/ProviderServiceController.php:286
* @route '/provider/services/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::destroy
* @see app/Http/Controllers/Provider/ProviderServiceController.php:286
* @route '/provider/services/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::destroy
* @see app/Http/Controllers/Provider/ProviderServiceController.php:286
* @route '/provider/services/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::toggleStatus
* @see app/Http/Controllers/Provider/ProviderServiceController.php:300
* @route '/provider/services/{id}/toggle-status'
*/
export const toggleStatus = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(args, options),
    method: 'post',
})

toggleStatus.definition = {
    methods: ["post"],
    url: '/provider/services/{id}/toggle-status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::toggleStatus
* @see app/Http/Controllers/Provider/ProviderServiceController.php:300
* @route '/provider/services/{id}/toggle-status'
*/
toggleStatus.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return toggleStatus.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::toggleStatus
* @see app/Http/Controllers/Provider/ProviderServiceController.php:300
* @route '/provider/services/{id}/toggle-status'
*/
toggleStatus.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::toggleStatus
* @see app/Http/Controllers/Provider/ProviderServiceController.php:300
* @route '/provider/services/{id}/toggle-status'
*/
const toggleStatusForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Provider\ProviderServiceController::toggleStatus
* @see app/Http/Controllers/Provider/ProviderServiceController.php:300
* @route '/provider/services/{id}/toggle-status'
*/
toggleStatusForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, options),
    method: 'post',
})

toggleStatus.form = toggleStatusForm

const ProviderServiceController = { index, create, store, show, edit, update, destroy, toggleStatus }

export default ProviderServiceController