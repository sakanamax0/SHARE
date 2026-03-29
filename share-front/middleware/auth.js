export default function ({ redirect }) {
  const isLoggedIn = process.client
    ? localStorage.getItem('isLoggedIn')
    : false

  if (!isLoggedIn) {
    return redirect('/login')
  }
}
