import React from 'react'
import { render, screen } from '@testing-library/react'
import Join from './Join'
import { MemoryRouter } from 'react-router-dom'

test('renders join page', () => {
  render(
    <MemoryRouter>
      <Join />
    </MemoryRouter>
  )

  expect(screen.getByText(/Join to Us/i)).toBeInTheDocument()
})
