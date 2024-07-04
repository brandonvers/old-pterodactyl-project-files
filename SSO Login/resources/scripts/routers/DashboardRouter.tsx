import React from 'react';
import { NavLink, Route, RouteComponentProps, Switch } from 'react-router-dom';
import AccountOverviewContainer from '@/components/dashboard/AccountOverviewContainer';
import NavigationBar from '@/components/NavigationBar';
import DashboardContainer from '@/components/dashboard/DashboardContainer';
import AccountApiContainer from '@/components/dashboard/AccountApiContainer';
import AccountOAuthContainer from '@/components/dashboard/AccountOAuthContainer';
import { NotFound } from '@/components/elements/ScreenBlock';
import TransitionRouter from '@/TransitionRouter';
import SubNavigation from '@/components/elements/SubNavigation';
import { useStoreState } from 'easy-peasy';

const RouterContainer = ({ location }: RouteComponentProps) => {

const { enabled: oauthEnabled } = useStoreState(state => state.settings.data!.oauth);

return (
		<>
			<NavigationBar/>
			{location.pathname.startsWith('/account') &&
			<SubNavigation>
				<div>
					<NavLink to={'/account'} exact>Settings</NavLink>
					<NavLink to={'/account/api'}>API Credentials</NavLink>
					{ oauthEnabled &&
					<NavLink to={'/account/oauth'}>OAuth</NavLink>
					}
				</div>
			</SubNavigation>
			}
			<TransitionRouter>
				<Switch location={location}>
					<Route path={'/'} exact>
						<DashboardContainer/>
					</Route>
					<Route path={'/account'} exact>
						<AccountOverviewContainer/>
					</Route>
					<Route path={'/account/api'} exact>
						<AccountApiContainer/>
					</Route>
					<Route path={'/account/oauth'} exact>
						<AccountOAuthContainer/>
					</Route>
					<Route path={'*'}>
						<NotFound/>
					</Route>
				</Switch>
			</TransitionRouter>
		</>
    );
};

export default RouterContainer;