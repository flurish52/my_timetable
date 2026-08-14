// navigation/adminNav.js
import { icons } from './icons'

export default [
    { to: '/admin', label: 'Dashboard', icon: icons.timetable },
    { to: '/admin/users', label: 'Users', icon: icons.users },
    { to: '/contributor', label: 'Contribute', icon: icons.upload },
    { to: '/pastquestions', label: 'Past Questions', icon: icons.pastQuestions },
    // { to: '/admin/settings', label: 'Settings', icon: icons.settings },
    { to: '/questions-of-the-day', label: 'Daily Q', icon: icons.qotd },
    { to: '/activity', label: 'Activity', icon: icons?.activity },
]
