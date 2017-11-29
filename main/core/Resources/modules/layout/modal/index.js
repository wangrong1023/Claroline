import React from 'react'
import invariant from 'invariant'

import {MessageModal} from './components/message.jsx'
import {ConfirmModal} from './components/confirm.jsx'
import {DeleteConfirmModal} from './components/delete-confirm.jsx'
import {UrlModal} from './components/url.jsx'
import {UserPickerModal} from './components/user-picker.jsx'
import {GenericTypePicker} from './components/generic-type-picker.jsx'
import {IframeModal} from './components/iframe.jsx'

export const MODAL_MESSAGE = 'MODAL_MESSAGE'
export const MODAL_CONFIRM = 'MODAL_CONFIRM'
export const MODAL_DELETE_CONFIRM = 'MODAL_DELETE_CONFIRM'
export const MODAL_URL = 'MODAL_URL'
export const MODAL_USER_PICKER = 'MODAL_USER_PICKER'
export const MODAL_GENERIC_TYPE_PICKER = 'MODAL_GENERIC_TYPE_PICKER'
export const MODAL_IFRAME = 'MODAL_IFRAME'

const modals = {
  [MODAL_MESSAGE]: MessageModal,
  [MODAL_CONFIRM]: ConfirmModal,
  [MODAL_DELETE_CONFIRM]: DeleteConfirmModal,
  [MODAL_URL]: UrlModal,
  [MODAL_USER_PICKER]: UserPickerModal, // todo : register it only in tools using it (users with no edit rights don't need it)
  [MODAL_GENERIC_TYPE_PICKER]: GenericTypePicker, // same here
  [MODAL_IFRAME]: IframeModal
}

export function registerModalType(type, component) {
  invariant(!modals[type], `Modal type ${type} is already registered`)
  modals[type] = component
}

export function registerModalTypes(types) {
  types.map(type => registerModalType(type[0], type[1]))
}

export function makeModal(type, props, fading, fadeCallback = () => true, hideCallback = () => true) {
  invariant(modals[type], `Unknown modal type "${type}"`)

  const baseProps = {
    show: !fading,
    fadeModal: () => fadeCallback(),
    hideModal:() => hideCallback()
  }

  return React.createElement(modals[type], Object.assign(baseProps, props))
}

export function makeModalFromUrl(fading, hideCallback = () => true, url) {
  return fetch(url, {method: 'GET', credentials: 'include'}).then(response => {
    return response.text()
  }).then(text => {
    const baseProps = {
      show: !fading,
      hideModal:() => hideCallback(),
      content: text
    }

    return React.createElement(UrlModal, baseProps)
  })
}
