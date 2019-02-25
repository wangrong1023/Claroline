import React, {Fragment} from 'react'
import {PropTypes as T} from 'prop-types'
import isEmpty from 'lodash/isEmpty'

import {trans} from '#/main/app/intl/translation'
import {EmptyPlaceholder} from '#/main/core/layout/components/placeholder'

import {User as UserType} from '#/main/core/user/prop-types'
import {UserCard} from '#/main/core/user/data/components/user-card'

const UsersDisplay = (props) => {
  if (!isEmpty(props.data)) {
    return (
      <Fragment>
        {props.data.map(user =>
          <UserCard
            key={`user-card-${user.id}`}
            data={user}
          />
        )}
      </Fragment>
    )
  }

  return (
    <EmptyPlaceholder
      size="lg"
      icon="fa fa-user"
      title={trans('no_user')}
    />
  )
}

UsersDisplay.propTypes = {
  data: T.arrayOf(T.shape(
    UserType.propTypes
  ))
}

export {
  UsersDisplay
}