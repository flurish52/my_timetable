// navigation/contributorNav.js
import { icons } from './icons'

export default [
    { to: '/dashboard', label: 'Timetable', icon: icons.timetable },
    { to: '/pastquestions', label: 'Past Questions', icon: icons.pastQuestions },
    { to: '/contributor', label: 'Contribute', icon: icons.upload },
    { to: '/questions-of-the-day', label: 'Daily Q', icon: icons.qotd },
    { to: '/activity', label: 'Activity', icon: icons?.activity },
]
