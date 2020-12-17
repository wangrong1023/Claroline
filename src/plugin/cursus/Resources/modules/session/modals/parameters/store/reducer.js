import {makeFormReducer} from '#/main/app/content/form/store'

import {selectors} from '#/plugin/cursus/session/modals/parameters/store/selectors'

export const reducer = makeFormReducer(selectors.STORE_NAME)
