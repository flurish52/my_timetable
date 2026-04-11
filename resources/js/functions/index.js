const { onSchedule } = require('firebase-functions/v2/scheduler')
const { onRequest } = require('firebase-functions/v2/https')
const { initializeApp } = require('firebase-admin/app')
const { getMessaging } = require('firebase-admin/messaging')

initializeApp()

// Scheduled: fires every weekday at 12:00 Lagos time
exports.lunchReminder = onSchedule('0 11 * * 1-5', async () => {
    await getMessaging().send({
        topic: 'org-broadcast',
        notification: {
            title: '🍽 Lunch time!',
            body: 'The cafeteria is now open'
        }
    })
})

// Manual trigger: call this URL from a browser or admin page
exports.sendNow = onRequest(async (req, res) => {
    const { title, body } = req.query
    await getMessaging().send({
        topic: 'org-broadcast',
        notification: { title: title || 'Reminder', body: body || '' }
    })
    res.send('Notification sent!')
})
