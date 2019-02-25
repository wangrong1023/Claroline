import React from 'react'
import {PropTypes as T} from 'prop-types'
import {connect} from 'react-redux'

import Carousel from 'react-bootstrap/lib/Carousel'

import {asset} from '#/main/app/config/asset'
import {trans} from '#/main/app/intl/translation'
import {EmptyPlaceholder} from '#/main/core/layout/components/placeholder'

import {selectors} from '#/plugin/slideshow/resources/slideshow/player/store'
import {Slide as SlideTypes} from '#/plugin/slideshow/resources/slideshow/prop-types'

const PlayerComponent = props => {
  if (0 === props.slides.length) {
    return (
      <EmptyPlaceholder
        size="lg"
        icon="fa fa-image"
        title={trans('no_slide', {}, 'slideshow')}
      />
    )
  }

  let activeIndex = props.slides.findIndex(slide => slide.id === props.activeSlide)
  if (-1 === activeIndex) {
    activeIndex = 0
  }

  return (
    <Carousel
      defaultActiveIndex={activeIndex}
      interval={props.autoPlay && props.interval}
      controls={props.showControls}
      indicators={props.showControls}

      prevIcon={<span className="fa fa-chevron-left" />}
      prevLabel={trans('previous')}
      nextIcon={<span className="fa fa-chevron-right" />}
      nextLabel={trans('next')}
    >
      {props.slides.map(slide =>
        <Carousel.Item
          key={slide.id}
        >
          <img src={asset(slide.content.url)} alt={slide.title} />

          {(slide.meta.title || slide.meta.description) &&
            <Carousel.Caption>
              {slide.meta.title &&
                <h3>{slide.meta.title}</h3>
              }

              {slide.meta.description &&
                <p>{slide.meta.description}</p>
              }
            </Carousel.Caption>
          }
        </Carousel.Item>
      )}
    </Carousel>
  )
}

PlayerComponent.propTypes = {
  activeSlide: T.string,
  autoPlay: T.bool.isRequired,
  interval: T.number.isRequired,
  showControls: T.bool,
  slides: T.arrayOf(T.shape(
    SlideTypes.propTypes
  )).isRequired
}

const Player = connect(
  (state) => ({
    autoPlay: selectors.autoPlay(state),
    interval: selectors.interval(state),
    showControls: selectors.showControls(state),
    slides: selectors.slides(state)
  })
)(PlayerComponent)

export {
  Player
}